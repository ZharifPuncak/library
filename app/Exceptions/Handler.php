<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (PostTooLargeException $e, $request) {
            \Log::warning('POST TOO LARGE', [
                'url'         => $request->fullUrl(),
                'content_len' => $request->header('Content-Length'),
                'php_upload'  => ini_get('upload_max_filesize'),
                'php_post'    => ini_get('post_max_size'),
            ]);
            $maxUpload = ini_get('upload_max_filesize') ?: 'the configured limit';

            return back()
                ->withInput($request->except(['files', 'thumbnail', 'slider_pic']))
                ->withErrors(['slider_pic' => "The uploaded file exceeds the current PHP upload limit ({$maxUpload})."]);
        });
    }
}
