import math
import numpy as np
from PIL import Image, ImageEnhance, ImageFilter, ImageDraw
import av
import os

def create_cinematic_card_video(image_path, output_path, duration_sec=5.0, fps=60):
    print(f"Loading image from {image_path}...")
    base_img = Image.open(image_path).convert("RGB")
    
    # Target resolution: 1280x720 HD
    target_w, target_h = 1280, 720
    
    # Resize base image with high-quality Lanczos to a slightly larger size for pan & tilt
    scale = 1.15
    render_w = int(target_w * scale)
    render_h = int(target_h * scale)
    base_img = base_img.resize((render_w, render_h), Image.Resampling.LANCZOS)
    
    total_frames = int(duration_sec * fps)
    print(f"Generating {total_frames} frames at {fps} fps ({target_w}x{target_h})...")
    
    container = av.open(output_path, mode='w')
    stream = container.add_stream('libx264', rate=fps)
    stream.width = target_w
    stream.height = target_h
    stream.pix_fmt = 'yuv420p'
    stream.options = {'crf': '18', 'preset': 'slow'}
    
    base_arr = np.array(base_img, dtype=np.float32)
    
    for frame_idx in range(total_frames):
        t = frame_idx / total_frames  # 0.0 to 1.0
        angle = t * 2.0 * math.pi
        
        # 1. Subtle camera breathing & floating tilt
        shift_x = math.sin(angle) * 22.0
        shift_y = math.cos(angle) * 12.0
        zoom = 1.0 + 0.04 * (1.0 - math.cos(angle)) / 2.0
        
        # Crop region coordinates
        center_x = render_w / 2.0 + shift_x
        center_y = render_h / 2.0 + shift_y
        
        crop_w = target_w / zoom
        crop_h = target_h / zoom
        
        x1 = max(0, int(center_x - crop_w / 2.0))
        y1 = max(0, int(center_y - crop_h / 2.0))
        x2 = min(render_w, int(x1 + crop_w))
        y2 = min(render_h, int(y1 + crop_h))
        
        frame_pil = base_img.crop((x1, y1, x2, y2)).resize((target_w, target_h), Image.Resampling.BICUBIC)
        frame_arr = np.array(frame_pil, dtype=np.float32)
        
        # 2. Dynamic Gold Foil Specular Light Gleam (sweeps across the card)
        # We simulate a directional light sweep moving from left to right
        sweep_pos = (math.sin(angle) + 1.0) / 2.0  # 0.0 to 1.0
        
        # Create gradient light mask
        x_coords = np.linspace(0, 1, target_w, dtype=np.float32)
        y_coords = np.linspace(0, 1, target_h, dtype=np.float32)
        xx, yy = np.meshgrid(x_coords, y_coords)
        
        # Diagonal beam equation: xx * 0.8 + yy * 0.4
        beam_dist = np.abs((xx * 0.85 + yy * 0.35) - (sweep_pos * 1.2))
        beam_intensity = np.exp(- (beam_dist ** 2) / (2 * (0.09 ** 2)))
        
        # Gold foil highlight boost: pixels that are already bright/golden get strong specular glint
        luminance = (frame_arr[:, :, 0] * 0.299 + frame_arr[:, :, 1] * 0.587 + frame_arr[:, :, 2] * 0.114) / 255.0
        gold_mask = np.clip((luminance - 0.25) * 1.6, 0.0, 1.0)
        
        # Warm golden specular color
        specular_r = beam_intensity * gold_mask * 110.0
        specular_g = beam_intensity * gold_mask * 90.0
        specular_b = beam_intensity * gold_mask * 35.0
        
        frame_arr[:, :, 0] = np.clip(frame_arr[:, :, 0] + specular_r, 0, 255)
        frame_arr[:, :, 1] = np.clip(frame_arr[:, :, 1] + specular_g, 0, 255)
        frame_arr[:, :, 2] = np.clip(frame_arr[:, :, 2] + specular_b, 0, 255)
        
        # 3. Subtle lens flare sparkle at peak reflection point
        if 0.3 < sweep_pos < 0.7:
            sparkle_t = math.sin((sweep_pos - 0.3) / 0.4 * math.pi)
            flare_x = int(target_w * (0.45 + (sweep_pos - 0.5) * 0.6))
            flare_y = int(target_h * 0.42)
            
            flare_dist = np.sqrt((np.arange(target_w) - flare_x)**2 + (np.arange(target_h)[:, None] - flare_y)**2)
            flare_glow = np.exp(- (flare_dist ** 2) / (2 * (40 ** 2))) * sparkle_t * 60.0
            
            frame_arr[:, :, 0] = np.clip(frame_arr[:, :, 0] + flare_glow * 1.0, 0, 255)
            frame_arr[:, :, 1] = np.clip(frame_arr[:, :, 1] + flare_glow * 0.85, 0, 255)
            frame_arr[:, :, 2] = np.clip(frame_arr[:, :, 2] + flare_glow * 0.4, 0, 255)
        
        final_img = Image.fromarray(frame_arr.astype(np.uint8))
        
        av_frame = av.VideoFrame.from_image(final_img)
        for packet in stream.encode(av_frame):
            container.mux(packet)
            
        if (frame_idx + 1) % 60 == 0:
            print(f"Rendered {frame_idx + 1}/{total_frames} frames...")
            
    for packet in stream.encode():
        container.mux(packet)
        
    container.close()
    print(f"Successfully created video at {output_path} (Size: {os.path.getsize(output_path)} bytes)")

if __name__ == "__main__":
    img_path = "uploads/products/veo_vip_showcase.jpg"
    out_path = "uploads/videos/tambaski_vip_showcase.mp4"
    create_cinematic_card_video(img_path, out_path, duration_sec=5.0, fps=60)
