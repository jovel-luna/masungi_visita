<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use App\Traits\Curl;
use App\Models\Books\Book;
use Illuminate\Http\UploadedFile;

class BookingObserver
{
    use Curl;
    
    /**
     * Handle the booking "created" event.
     *
     * @param  \App\Book  $book
     * @return void
     */
    public function created(Book $book)
    {
        # Generate QR via google api
        $qr_id = $book->generateRandomString();
        $json = json_encode($qr_id);
        $url = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . $json;

        $info = pathinfo($url);
        
        // $contents = file_get_contents($url);
        $contents = $this->curl_get_file_contents($url);

        $file = 'tmp/' . $info['basename'];
        // $this->file_force_contents($file, $contents);
        $dir = base_path($file);
        $dir = str_replace('\\', '/', $dir);
        file_put_contents($file, $contents);
        $uploaded_file = new UploadedFile($file, $info['basename']);

        $path = $uploaded_file->store('public/qr_codes');
        $book->update(['qr_code_path' => $path, 'qr_id' => $qr_id]);
    }

    // private function file_force_contents($dir, $contents){
    //     // Get the base directory path
    //     $baseDir = base_path(); // For Laravel applications

    //     // Combine the base directory path with the directory path
    //     $fullDir = $baseDir . '/' . $dir;
    //     Log::info('Directory Path: ' . base_path($dir));
    //     $parts = explode('/', $dir);
    //     $file = array_pop($parts);
    //     $dir = '';
    //     foreach($parts as $part){
    //         if(!is_dir($dir .= "/$part")) mkdir(base_path($dir));
    //     }

    //     file_put_contents("$dir/$file", $contents, 0777 ,true);
    // }

    /**
     * Replacement for file_get_contenst due to security reasons
     * 
     */
    private function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;
    }
}
