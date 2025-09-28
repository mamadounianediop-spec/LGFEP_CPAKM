<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - CPAKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts pour polices professionnelles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .cpakm-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            letter-spacing: 0.1em;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
            position: relative;
        }
        
        .cpakm-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #1e40af 0%, #06b6d4 100%);
            border-radius: 2px;
        }
        
        .developer-signature {
            font-family: 'Inter', sans-serif;
            color: #6b7280;
        }
        
        .pride-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }
        
        .login-container {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .bg-gradient-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">
<div class="min-h-screen flex flex-col justify-center py-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Décoration d'arrière-plan -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- En-tête -->
        <div class="text-center">
            <div class="mb-4">
                <h1 class="cpakm-title text-xl sm:text-2xl lg:text-3xl mb-2">
                    CPAKM
                </h1>
                <p class="text-xs sm:text-sm text-gray-700 font-medium px-4">
                    Gestion Financière
                </p>
                <p class="text-xs text-gray-600 mt-1 px-4">
                    Cours Privés Abdou Khadre Mbacké
                </p>
            </div>
        </div>
    </div>

    <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="login-container py-4 px-4 shadow-xl sm:rounded-xl sm:px-8">
            <form class="space-y-4" action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <div class="mt-1">
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required
                            value="{{ old('email') }}"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Entrez votre email">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <div class="mt-1">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Entrez votre mot de passe">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <!-- Bouton -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-3 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} CPAKM - Cours Privés Abdou Khadre Mbacké
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Accès réservé au personnel autorisé de l'établissement
            </p>
        </div>
        
        <!-- Signature du développeur -->
        <div class="mt-4 text-center pb-2">
            <div class="developer-signature">
                <p class="text-sm font-medium mb-2">
                    <span class="pride-text">Fièrement développé</span> pour CPAKM
                </p>
                <div class="border-t border-gray-200 pt-3">
                    <p class="text-gray-600 text-xs font-semibold">
                        <i class="fas fa-code mr-1 text-blue-500"></i>
                        Niane DIOP
                    </p>
                    <p class="text-gray-500 text-xs mt-1 leading-relaxed">
                        Analyste • Développeur • Professeur • Planificateur
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>