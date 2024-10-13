@vite([
  'resources/assets/vendor/js/bootstrap.js',
])

@yield('vendor-script')

@vite([ 'resources/js/hiding.js',])

@stack('pricing-script')

@yield('page-script')
