<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Your Account — SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .register-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            background: #fff;
            max-width: 1180px;
            margin: 0 auto;
        }
        .register-card-body {
            padding: 3rem;
        }
        .plan-card {
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
        }
        .plan-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .plan-card.selected {
            border-color: #667eea;
            background: #f8f9ff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        .plan-card input[type="radio"] {
            display: none;
        }
        .plan-card .price {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        .plan-card .period {
            color: #6c757d;
            font-size: 0.85rem;
        }
        .plan-card .feature-item {
            font-size: 0.85rem;
            padding: 2px 0;
        }
        .plan-card .feature-item i {
            width: 18px;
        }
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        .input-group-text {
            background: #f8f9fa;
            border-radius: 12px 0 0 12px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.85rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .section-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        .logo-text {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-text h1 {
            font-weight: 800;
            color: #333;
        }
        .logo-text p {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-card-body">
            <div class="logo-text">
                <h1><i class="fas fa-hotel text-primary me-2"></i>Reservation+HRM</h1>
                <p>Start your 14-day free trial. No credit card required.</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('tenant.register') }}">
                @csrf

                <h5 class="section-title">1. Choose your plan</h5>
                <div class="row g-3 mb-4">
                    @foreach ($plans as $plan)
                        <div class="col-sm-6 col-xl-3">
                            <label class="plan-card @selected(old('plan_id') == $plan->id) selected" id="plan-label-{{ $plan->id }}">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}"
                                    {{ old('plan_id') == $plan->id ? 'checked' : '' }}
                                    onchange="document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected')); this.closest('.plan-card').classList.add('selected');"
                                    {{ $loop->first ? 'checked' : '' }}>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ $plan->name }}</h6>
                                </div>
                                <div class="mb-2">
                                    <span class="price">SAR {{ $formattedPrice = number_format($plan->price, 0) }}</span>
                                    <span class="period">/ {{ $plan->billing_period }}</span>
                                </div>
                                <div>
                                    <div class="feature-item">
                                        <i class="fas fa-users text-success me-1"></i>
                                        {{ $plan->maxLimit('max_users') ?: 'Unlimited' }} users
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-building text-success me-1"></i>
                                        {{ $plan->maxLimit('max_properties') ?: 'Unlimited' }} properties
                                    </div>
                                    @if (in_array('custom_branding', $plan->features ?? [], true))
                                        <div class="feature-item">
                                            <i class="fas fa-palette text-success me-1"></i>
                                            Custom Branding
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('plan_id')
                    <div class="text-danger small mb-3">{{ $message }}</div>
                @enderror

                <h5 class="section-title">2. Your company</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                            class="form-control" placeholder="Grand Hyatt" required>
                        @error('company_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subdomain</label>
                        <div class="input-group">
                            <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                                class="form-control" placeholder="grand-hyatt" required>
                            <span class="input-group-text">.yourplatform.com</span>
                        </div>
                        @error('subdomain') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h5 class="section-title">3. Your account</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Your Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control" placeholder="John Doe" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control" placeholder="john@example.com" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                            class="form-control" placeholder="Min. 8 characters" required>
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="form-control" placeholder="Repeat password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-rocket me-2"></i>Create Free Account
                </button>

                <p class="text-center text-muted small mt-3 mb-0">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        // Preselect first plan on page load
        document.addEventListener('DOMContentLoaded', function () {
            const firstRadio = document.querySelector('input[name="plan_id"]');
            if (firstRadio && !document.querySelector('input[name="plan_id"]:checked')) {
                firstRadio.checked = true;
                firstRadio.closest('.plan-card')?.classList.add('selected');
            }
        });
    </script>
</body>
</html>
