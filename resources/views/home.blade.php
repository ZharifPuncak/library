@extends('layouts.app')

@section('title', 'Home')

@section('content')
<style>
    /* Hero Section with Slideshow */
    .hero-section { position: relative; height: calc(100vh - 5rem); min-height: 500px; overflow: hidden; }
    @media (max-width: 768px) {
        .hero-section { height: calc(100vh - 4rem); }
    }
    .hero-slide { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; transition: opacity 2s ease-in-out; }
    .hero-slide.active { opacity: 1; }
    .hero-slide-bg { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center center; }
    
    .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.4) 100%); display: flex; align-items: center; justify-content: center; }
    .hero-content { text-align: center; color: white; z-index: 10; padding: 0 1rem; }
    .hero-title { font-size: clamp(2rem, 8vw, 4rem); font-weight: 900; margin-bottom: 1rem; text-shadow: 0 4px 12px rgba(0,0,0,0.3); letter-spacing: -0.02em; }
    .hero-subtitle { font-size: clamp(1rem, 3vw, 1.25rem); margin-bottom: 2rem; opacity: 0.9; font-weight: 500; font-family: 'Inter', sans-serif; }
    
    /* Slide Controls */
    .slide-nav { position: absolute; bottom: 30px; left: 0; width: 100%; display: flex; justify-content: center; gap: 8px; z-index: 20; }
    .slide-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.3); cursor: pointer; transition: all 0.3s; }
    .slide-dot.active { background: white; width: 24px; border-radius: 4px; }
    
    .slide-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: white/10; color: white; border: none; font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 50%; transition: all 0.3s; z-index: 20; backdrop-filter: blur(8px); border: 1px solid white/20; }
    .slide-arrow:hover { background: white; color: #0e3f70; }
    .slide-arrow.prev { left: 20px; }
    .slide-arrow.next { right: 20px; }

    /* Quick Access Cards */
    .access-card {
        @apply transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl border border-white/10;
        background: linear-gradient(135deg, #0e3f70 0%, #1e4972 100%);
    }
</style>

<!-- Hero Slideshow -->
<section class="hero-section">
    @if(isset($slideshows) && $slideshows->count() > 0)
        @foreach($slideshows as $index => $slide)
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                <img class="hero-slide-bg" src="{{ asset('storage/' . $slide->slideshow_pic) }}" alt="{{ $slide->title }}">
            </div>
        @endforeach
    @else
        <div class="hero-slide active">
            <img class="hero-slide-bg" src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2000&auto=format&fit=crop" alt="Library">
        </div>
        <div class="hero-slide">
            <img class="hero-slide-bg" src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=2000&auto=format&fit=crop" alt="Bookshelf">
        </div>
    @endif

    <div class="hero-overlay">
        <div class="hero-content">
            <h1 class="hero-title uppercase">Digital Repository</h1>
            <p class="hero-subtitle">Puncak Niaga Library Portal</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('media.index') }}" class="bg-white text-lib-navy px-8 py-3 rounded-full font-bold hover:bg-lib-sky hover:text-white transition-all shadow-lg">Browse Media</a>
            </div>
        </div>
    </div>

    <button class="slide-arrow prev" onclick="changeSlide(-1)">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
    </button>
    <button class="slide-arrow next" onclick="changeSlide(1)">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </button>

    <div class="slide-nav">
        @if(isset($slideshows) && $slideshows->count() > 0)
            @foreach($slideshows as $index => $slide)
                <span class="slide-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></span>
            @endforeach
        @else
            <span class="slide-dot active" onclick="goToSlide(0)"></span>
            <span class="slide-dot" onclick="goToSlide(1)"></span>
        @endif
    </div>
</section>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slide-dot');

    function showSlide(n) {
        if (slides.length === 0) return;
        if (n >= slides.length) currentSlide = 0;
        else if (n < 0) currentSlide = slides.length - 1;
        else currentSlide = n;
        
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        slides[currentSlide].classList.add('active');
        if(dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    function changeSlide(n) {
        showSlide(currentSlide + n);
    }

    function goToSlide(n) {
        showSlide(n);
    }

    // Auto-advance
    setInterval(() => changeSlide(1), 10000);
</script>
@endsection