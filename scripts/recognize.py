import sys
import json
import wave
import os
import re
from vosk import Model, KaldiRecognizer
from pydub import AudioSegment
from deepmultilingualpunctuation import PunctuationModel

# Загружаем модель пунктуации один раз при старте
# (это долго — около 30 секунд при первом запуске, потом быстро)
print("[INFO] Загрузка модели пунктуации...", file=sys.stderr)
punctuation_model = PunctuationModel()
print("[INFO] Модель пунктуации загружена.", file=sys.stderr)


def convert_to_wav(input_path):
    """Конвертирует MP3 (или другой формат) в WAV 16 кГц, моно."""
    if input_path.lower().endswith('.wav'):
        return input_path

    audio = AudioSegment.from_file(input_path)
    audio = audio.set_frame_rate(16000).set_channels(1).set_sample_width(2)

    wav_path = input_path.rsplit('.', 1)[0] + '_converted.wav'
    audio.export(wav_path, format='wav')
    return wav_path


def restore_case(text):
    """
    Поднимает регистр: первая буква предложения заглавная,
    местоимение 'i' -> 'I'.
    """
    # 'i' -> 'I' (только как отдельное слово)
    text = re.sub(r'\bi\b', 'I', text)

    # Первая буква после точки, вопросительного или восклицательного знака
    # + начало текста
    def capitalize_after(match):
        return match.group(1) + match.group(2).upper()

    text = re.sub(r'(^|[.!?]\s+)([a-zа-я])', capitalize_after, text)

    return text


def recognize(audio_path, model_path):
    # Конвертируем в WAV, если нужно
    wav_path = convert_to_wav(audio_path)

    wf = wave.open(wav_path, "rb")

    if wf.getnchannels() != 1 or wf.getsampwidth() != 2 or wf.getframerate() != 16000:
        return json.dumps({"error": "Аудио должно быть WAV, 16 кГц, моно"})

    # Распознавание речи
    model = Model(model_path)
    recognizer = KaldiRecognizer(model, wf.getframerate())

    result_text = []
    while True:
        data = wf.readframes(4000)
        if len(data) == 0:
            break
        if recognizer.AcceptWaveform(data):
            res = json.loads(recognizer.Result())
            if res.get("text"):
                result_text.append(res["text"])

    final = json.loads(recognizer.FinalResult())
    if final.get("text"):
        result_text.append(final["text"])

    raw_text = " ".join(result_text).strip()

    if not raw_text:
        return json.dumps({"text": ""})

    # Расстановка пунктуации
    try:
        punctuated = punctuation_model.restore_punctuation(raw_text)
    except Exception as e:
        print(f"[WARN] Ошибка пунктуации: {e}", file=sys.stderr)
        punctuated = raw_text

    # Поднимаем регистр
    final_text = restore_case(punctuated)

    return json.dumps({"text": final_text}, ensure_ascii=False)


if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Использование: recognize.py <audio> <model>"}))
        sys.exit(1)

    audio_path = sys.argv[1]
    model_path = sys.argv[2]
    print(recognize(audio_path, model_path))