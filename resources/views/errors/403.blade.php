@include('errors.minimal', [
    'code'    => '403',
    'title'   => 'Access denied',
    'message' => "You don't have permission to view that page. If you think this is a mistake, please sign in or contact your administrator.",
    'cta'     => [route('login') => 'Sign in'],
])
