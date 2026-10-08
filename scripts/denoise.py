import sys
from pyrnnoise import RNNoise

def denoise(input_path, output_path):
    denoiser = RNNoise(sample_rate=16000)
    for _ in denoiser.denoise_wav(input_path, output_path):
        pass
    return output_path

if __name__ == "__main__":
    input_path = sys.argv[1]
    output_path = sys.argv[2]
    print(denoise(input_path, output_path))