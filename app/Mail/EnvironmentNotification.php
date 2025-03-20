<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\EnvironmentSensor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvironmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $temperature;
    public $humidity;

    // Constructor to pass data to the mailable
    public function __construct(Product $product, EnvironmentSensor $temperature,EnvironmentSensor $humidity)
    {
        $this->product = $product;
        $this->temperature = $temperature;
        $this->humidity = $humidity;
    }

    // Build the email
    public function build()
    {
        return $this->subject('Environment Alert: Unsuitable Conditions Detected')
                    ->view('emails.environment_notification')
                    ->with([
                        'product' => $this->product,
                        'temperature' => $this->temperature,
                        'humidity' => $this->humidity,
                    ]);
    }
}
