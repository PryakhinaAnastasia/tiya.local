import sys
import json
import os
import re
from faster_whisper import WhisperModel
from pydub import AudioSegment
from deepmultilingualpunctuation import PunctuationModel

# Модель Whisper (загружается один раз при старте)
# "small" — хороший баланс скорости и точности, весит ~500 МБ
# Первый запуск скачает модель в кэш (~/.cache/huggingface)
print("[INFO] Загрузка модели Whisper...", file=sys.stderr)
whisper_model = WhisperModel("small", device="cpu", compute_type="int8")
print("[INFO] Whisper загружен.", file=sys.stderr)

# Модель пунктуации (загружается один раз)
print("[INFO] Загрузка модели пунктуации...", file=sys.stderr)
punctuation_model = PunctuationModel()
print("[INFO] Модель пунктуации загружена.", file=sys.stderr)


def convert_to_wav(input_path):
    """Конвертирует любой формат (MP3, M4A, WAV) в WAV 16 кГц, моно."""
    if input_path.lower().endswith('.wav'):
        # Проверим параметры — Whisper сам может работать с разными WAV,
        # но лучше привести к 16 кГц, моно
        try:
            audio = AudioSegment.from_file(input_path)
            if audio.frame_rate == 16000 and audio.channels == 1:
                return input_path
        except Exception:
            pass

    audio = AudioSegment.from_file(input_path)
    audio = audio.set_frame_rate(16000).set_channels(1).set_sample_width(2)

    wav_path = input_path.rsplit('.', 1)[0] + '_converted.wav'
    audio.export(wav_path, format='wav')
    return wav_path


def restore_case(text):
    """Поднимает регистр: первая буква предложения заглавная, 'i' → 'I'."""
    text = re.sub(r'\bi\b', 'I', text)
    text = re.sub(r'(^|[.!?]\s+)([a-zа-я])', lambda m: m.group(1) + m.group(2).upper(), text)
    return text


def recognize(audio_path, language='en'):
    # Конвертируем в WAV, если нужно
    wav_path = convert_to_wav(audio_path)

    # Распознавание через Whisper
    # language='en' — явно указываем английский (для ЕГЭ)
    segments, info = whisper_model.transcribe(
        wav_path,
        language=language,
        beam_size=5,
        vad_filter=True,  # фильтр тишины
    )

    # Собираем все сегменты в один текст
    raw_text = " ".join(segment.text.strip() for segment in segments).strip()

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

    # Чистим временный WAV-файл
    if wav_path != audio_path and os.path.exists(wav_path):
        try:
            os.remove(wav_path)
        except Exception:
            pass

    return json.dumps({"text": final_text}, ensure_ascii=False)


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Использование: recognize.py <audio>"}))
        sys.exit(1)

    audio_path = sys.argv[1]
    # второй аргумент (модель Vosk) больше не нужен, но оставим для совместимости
    print(recognize(audio_path))