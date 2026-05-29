@include('errors.minimal', [
    'code'    => '419',
    'title'   => 'Session expired',
    'message' => 'For your security, the page expired after a period of inactivity. Please reload and try again.',
    'cta'     => [url()->previous() => 'Go back'],
])
