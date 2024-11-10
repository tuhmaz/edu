@php
$containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme background bg-black">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        © <script>
          document.write(new Date().getFullYear())
        </script>, made with by <a href="{{config('settings.site_name')}}" target="_blank" class="footer-link">{{config('settings.site_name')}}</a>
      </div>
      <div class="d-none d-lg-inline-block ">
        <a href="{{ config('settings.whatsapp') }}" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/whatsapp.webp')}}" alt="whatsapp icon" style="height: 30px;" />
        </a>

        <a href="{{ config('settings.tiktok') }}" class="me-3" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/tiktok.webp')}}" alt="tiktok icon" style="height: 30px;" />
        </a>
        <a href="{{ config('settings.facebook') }}" class="me-3" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/facebook.webp')}}" alt="facebook icon" style="height: 30px;" />
        </a>
        <a href="{{ config('settings.twitter') }}" class="me-3 text black" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/twitter.webp')}}" alt="twitter icon" style="height: 30px;" />
        </a>
        <a href="{{ config('settings.linkedin') }}" target="_blank">
          <img src="{{asset('assets/img/front-pages/icons/linkedin.webp')}}" alt="linkedin icon" style="height: 30px;" />
        </a>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->