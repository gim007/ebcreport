@include('errors.minimal', [
    'code'    => '429',
    'title'   => 'Too many requests',
    'message' => "You've made too many requests in a short period. Please wait a minute and try again.",
])
