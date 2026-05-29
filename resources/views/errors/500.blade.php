@include('errors.minimal', [
    'code'    => '500',
    'title'   => 'Something went wrong',
    'message' => "We hit an unexpected problem on our end. The team has been notified. Please try again in a moment.",
    'cta'     => [url('/') => 'Back to home'],
])
