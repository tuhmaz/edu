<footer class="landing-footer bg-body footer-text">
  <div class="footer-top position-relative overflow-hidden z-1">
    <img src="{{asset('assets/img/front-pages/backgrounds/footer-bg-'.$configData['style'].'.png')}}" alt="footer bg" class="footer-bg banner-bg-img z-n1" data-app-light-img="front-pages/backgrounds/footer-bg-light.png" data-app-dark-img="front-pages/backgrounds/footer-bg-dark.png" />
    <div class="container">
      <div class="row gx-0 gy-6 g-lg-10">
      <div class="col-lg-5">
  <a href="javascript:;" class="app-brand-link mb-6 d-flex align-items-center">
    <span class="app-brand-logo">
      <img src="{{ asset('storage/' . config('settings.site_logo')) }}" alt="LogoWebsite" style="max-width: 50px; height: auto;">
    </span>
    <span class="app-brand-text footer-link fw-bold ms-2 ps-1">
      {{ config('settings.site_name') }}
    </span>
  </a>
  <p class="footer-text footer-logo-description mb-6">
    {{ config('settings.site_description') }}
  </p>
</div>

        <div class="col-lg-3 col-md-4">
          <h6 class="footer-title mb-6">Download our app</h6>
          <a href="javascript:void(0);" class="d-block mb-4"><img src="{{asset('assets/img/front-pages/landing-page/apple-icon.png')}}" alt="apple icon" /></a>
          <a href="javascript:void(0);" class="d-block"><img src="{{asset('assets/img/front-pages/landing-page/google-play-icon.png')}}" alt="google play icon" /></a>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3 py-md-5">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-bottom-text">©
          <script>
          document.write(new Date().getFullYear());

          </script>
        </span>

        <a href="{{config('settings.site_name')}}" target="_blank" class="fw-medium text-white text-white">{{config('settings.site_name')}},</a>
        <span class="footer-bottom-text"> {{ __('All rights reserved') }}.</span>
      </div>
      <div>
      <a href="{{ config('settings.whatsapp') }}" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/whatsapp.svg')}}" alt="whatsapp icon" />
        </a>

        <a href="{{ config('settings.tiktok') }}" class="me-3" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/tiktok.svg')}}" alt="tiktok icon" />
        </a>
        <a href="{{ config('settings.facebook') }}" class="me-3" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/facebook.svg')}}" alt="facebook icon" />
        </a>
        <a href="{{ config('settings.twitter') }}" class="me-3" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/twitter.svg')}}" alt="twitter icon" />
        </a>
        <a href="{{ config('settings.linkedin') }}" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/linkedin.svg')}}" alt="linkedin icon" />
        </a>
      </div>
    </div>
  </div>
</footer>
