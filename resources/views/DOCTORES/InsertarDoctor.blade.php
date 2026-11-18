<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Doctor</title>
    <style>
        body {
            /* background-image: url('{{ asset('NDBackground.png') }}');
            background-position: calc(50% + 120px) center;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh; */
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            width: calc(100% - 240px);
            min-height: 100vh;
            margin-left: 240px;
            padding: 40px;
            box-sizing: border-box;
        }

        h1 {
            color: black;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }

        button {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit {
            background-color: #03162E;
            color: white;
        }

        .btn-submit:hover {
            background-color: #0a2c5a;
        }

        .btn-cancel {
            background-color: #ccc;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #999;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
        }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #304766;
            position: fixed;
            top: 0;
            left: 0;
            padding: 16px 12px;
            box-sizing: border-box;
            border-right: 1px solid #043f75;
        }

        .sidebar a {
            text-decoration: none;
            color: #121629;
            padding: 12px 16px;
            border-radius: 24px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.2s;
        }

        .sidebar a:hover {
            background-color: #7C99D9;
        }

        .sidebar a.active {
            background-color: #03162E;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div style="margin-top: 20px;">
            <a href="{{ route('menu-principal') }}" style="border-radius: 0;">Doctores</a>
        </div>
    </nav>

    <div class="container">
        <h1>Agregar Nuevo Doctor</h1>

        @if ($errors->any())
            <div class="form-container" style="background-color: #fee; border: 1px solid #fcc;">
                <h4 style="color: #c33;">Errores en el formulario:</h4>
                <ul style="color: #c33;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-container">
            <form action="{{ route('doctores.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo">Código:</label>
                        <input type="number" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                        @error('codigo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" maxlength="50" required>
                        @error('nombre') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}" required>
                        @error('direccion') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" maxlength="20" required>
                        @error('telefono') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_nac">Fecha de Nacimiento:</label>
                        <input type="date" id="fecha_nac" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                        @error('fecha_nac') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo:</label>
                        <input type="text" id="sexo" name="sexo" value="{{ old('sexo') }}" maxlength="15" required>
                        @error('sexo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="especialidad">Especialidad:</label>
                    <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad') }}" required>
                    @error('especialidad') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                    @error('contraseña') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Agregar Doctor</button>
                    <a href="{{ route('menu-principal') }}" class="btn-cancel" style="text-decoration: none; display: flex; align-items: center; padding: 12px 24px;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>