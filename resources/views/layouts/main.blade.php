<!DOCTYPE html>
<html lang="en">

@include('partials.header')

<body id="page-top">

  <div id="wrapper">
    {{-- @include('partials.sidebar') --}}
    <div id="content-wrapper" class="content d-flex flex-column">
      <div id="content" class="mb-4">
        @include('partials.navbar')

        <div class="container-fluid">
          @yield('content')
        </div>

      </div>
      {{-- @include('partials.footer') --}}
    </div>
  </div>

</body>
</html>