@include('errors.minimal', [
    'code'    => '404',
    'title'   => 'Page not found',
    'message' => "We couldn't find the page you were looking for. It may have been moved or deleted.",
    'cta'     => [url('/') => 'Back to home'],
])
