import sys
import json
import wave
import os
from vosk import Model, KaldiRecognizer
from pydub import AudioSegment

def convert_to_wav(input_path):
    """Конвертирует MP3 (или другой формат) в WAV 16 кГц, моно."""
    if input_path.lower().endswith('.wav'):
        return input_path

    # Конвертируем в WAV
    audio = AudioSegment.from_file(input_path)
    audio = audio.set_frame_rate(16000).set_channels(1).set_sample_width(2)

    wav_path = input_path.rsplit('.', 1)[0] + '_converted.wav'
    audio.export(wav_path, format='wav')
    return wav_path

def recognize(audio_path, model_path):
    # Конвертируем, если нужно
    wav_path = convert_to_wav(audio_path)

    wf = wave.open(wav_path, "rb")

    if wf.getnchannels() != 1 or wf.getsampwidth() != 2 or wf.getframerate() != 16000:
        return json.dumps({"error": "Аудио должно быть WAV, 16 кГц, моно"})

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

    return json.dumps({"text": " ".join(result_text).strip()})

if __name__ == "__main__":
    audio_path = sys.argv[1]
    model_path = sys.argv[2]
    print(recognize(audio_path, model_path))