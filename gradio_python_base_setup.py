import gradio as gr
import numpy as np
from PIL import Image
import base64
from io import BytesIO

def random_image(width, height):
    # Erstelle ein zufälliges Bild mit den gewünschten Dimensionen
    array = np.random.randint(0, 256, (height, width, 3), dtype=np.uint8)
    img = Image.fromarray(array)
    
    # Konvertiere das Bild zu Base64
    buffered = BytesIO()
    img.save(buffered, format="PNG")
    img_str = "data:image/png;base64," + base64.b64encode(buffered.getvalue()).decode()
    
    return img_str

# Erweiterte Interface mit Dimensionseingabe
iface = gr.Interface(
    fn=random_image,
    inputs=[
        gr.Slider(32, 1024, value=256, label="Breite"),
        gr.Slider(32, 1024, value=256, label="Höhe")
    ],
    outputs="text",
    title="Custom Image Generator",
    description="Generiert ein zufälliges Bild mit angepassten Dimensionen als Base64 String"
)

if __name__ == "__main__":
    iface.launch(
        server_name="0.0.0.0",
        server_port=7861,
        share=True
    )
