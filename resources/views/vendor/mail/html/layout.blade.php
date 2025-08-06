<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
:root {
  --primary-color: #1a5a8d;
  --secondary-color: #0b2e59;
  --accent-color: #ffb300;
  --light-color: #f4f6f9;
  --text-color: #333333;
  --text-light: #718096;
}

body {
  background-color: #f4f6f9;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', 'Roboto', sans-serif;
  color: var(--text-color);
  line-height: 1.5;
}

.email-container {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.email-header {
  background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
  padding: 30px 20px;
  text-align: center;
}

.email-header img {
  max-width: 200px;
  height: auto;
}

.email-body {
  padding: 30px;
}

.email-footer {
  background: var(--light-color);
  padding: 20px;
  text-align: center;
  border-top: 1px solid #e0e0e0;
  font-size: 12px;
  color: var(--text-light);
}

h1, h2, h3 {
  color: var(--primary-color);
  margin-top: 0;
}

h1 {
  font-size: 24px;
  margin-bottom: 20px;
}

p {
  margin-bottom: 15px;
  font-size: 16px;
  line-height: 1.6;
}

.button {
  display: inline-block;
  background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
  color: white;
  text-decoration: none;
  padding: 12px 30px;
  border-radius: 6px;
  font-weight: 600;
  margin: 20px 0;
  text-align: center;
  transition: all 0.3s;
  box-shadow: 0 4px 10px rgba(26, 90, 141, 0.2);
}

.button:hover {
  background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
  box-shadow: 0 6px 15px rgba(26, 90, 141, 0.3);
  transform: translateY(-2px);
}

.signature {
  margin-top: 30px;
  border-top: 1px solid #eee;
  padding-top: 20px;
  color: var(--text-light);
}

@media only screen and (max-width: 600px) {
  .email-container {
    border-radius: 0;
    box-shadow: none;
  }
  
  .email-body {
    padding: 20px;
  }
  
  h1 {
    font-size: 22px;
  }
}
</style>
{!! $head ?? '' !!}
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <img src="{{ asset('Imagen/Posface_logo.jpeg') }}" alt="POSFACE Logo">
      <h1>Sistema de Gestión Académica</h1>
    </div>
    
    <div class="email-body">
      @yield('content')
    </div>
    
    <div class="email-footer">
      <p>&copy; {{ date('Y') }} POSFACE - Universidad Nacional Autónoma de Honduras</p>
      <p>Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
    </div>
  </div>
</body>
</html>