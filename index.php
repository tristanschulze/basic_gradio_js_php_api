<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JS/PHP 2 GRADIO API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .control-panel {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="number"] {
            width: 100px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        #image-container {
            margin-top: 20px;
            text-align: center;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #ccc;
            border-radius: 8px;
            position: relative;
        }
        #loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #generated-image {
            max-width: 100%;
            max-height: 600px;
        }
    </style>
</head>
<body>
    
    <div class="control-panel">
        <div class="form-group">
            <label for="width">Breite (px):</label>
            <input type="number" id="width" min="32" max="1024" value="256">
        </div>
        <div class="form-group">
            <label for="height">Höhe (px):</label>
            <input type="number" id="height" min="32" max="1024" value="256">
        </div>
        <button id="generate-btn">Bild generieren</button>
    </div>
    
    <div id="image-container">
        <div id="loading-overlay">
            <div class="spinner"></div>
            <p>Bild wird generiert...</p>
        </div>
        <img id="generated-image" style="display: none;">
    </div>

    <script>
        document.getElementById('generate-btn').addEventListener('click', function() {
            const width = document.getElementById('width').value;
            const height = document.getElementById('height').value;
            const imageContainer = document.getElementById('image-container');
            const loadingOverlay = document.getElementById('loading-overlay');
            const imgElement = document.getElementById('generated-image');
            
            // Loading anzeigen
            loadingOverlay.style.display = 'flex';
            imgElement.style.display = 'none';
            
            // API aufrufen
            fetch('call_gradio_api.php?width=' + width + '&height=' + height)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.blob();
                })
                .then(blob => {
                    const imageUrl = URL.createObjectURL(blob);
                    imgElement.src = imageUrl;
                    imgElement.style.display = 'block';
                    loadingOverlay.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingOverlay.innerHTML = '<p style="color:red">Fehler: ' + error.message + '</p>';
                });
        });
    </script>
</body>
</html>
