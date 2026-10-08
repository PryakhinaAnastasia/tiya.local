import sys
import soundfile as sf
import numpy as np
from pyrnnoise import RNNoise

def denoise(input_path, output_path):
    # 1. Читаем WAV-файл через soundfile (это работает)
    # soundfile возвращает массив с формой (samples,) для моно
    audio_data, rate = sf.read(input_path, dtype='int16')

    # 2. Проверяем, что это 16 кГц (RNNoise работает с этим)
    if rate != 16000:
        print(f"Предупреждение: частота {rate} Гц, RNNoise ожидает 16000 Гц", file=sys.stderr)

    # 3. Приводим к форме, которую ждёт denoise_chunk: [channels, samples]
    # Если аудио моно, shape будет (samples,), делаем (1, samples)
    if audio_data.ndim == 1:
        audio_data = audio_data.reshape(1, -1)

    # 4. Создаём денойзер и обрабатываем чанк
    denoiser = RNNoise(sample_rate=rate)
    output_chunks = []

    # denoise_chunk возвращает генератор (speech_prob, denoised_frame)
    for speech_prob, denoised_frame in denoiser.denoise_chunk(audio_data):
        output_chunks.append(denoised_frame)

    # 5. Склеиваем все обработанные фреймы в один массив
    if output_chunks:
        # np.concatenate по последней оси (samples)
        cleaned_audio = np.concatenate(output_chunks, axis=-1)
    else:
        cleaned_audio = audio_data

    # 6. Возвращаем к 1D для soundfile, если было моно
    if cleaned_audio.shape[0] == 1:
        cleaned_audio = cleaned_audio.flatten()

    # 7. Сохраняем результат
    sf.write(output_path, cleaned_audio, rate)

    return output_path

if __name__ == "__main__":
    input_path = sys.argv[1]
    output_path = sys.argv[2]
    print(denoise(input_path, output_path))