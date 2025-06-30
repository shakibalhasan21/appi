<?php

// Function to fetch and directly output API response
function getInfo($nid, $dob) {
    $api = 'https://api-store.top/svbalance/Api.php?key=page&nid=' . $nid . '&dob=' . $dob;

    // Initialize cURL
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36'
    ));

    // Execute cURL request
    $content = curl_exec($curl);

    // Handle errors
    if (curl_errno($curl)) {
        echo 'cURL Error: ' . curl_error($curl);
    } else {
        // Modify the response
        $data = json_decode($content, true);
        if ($data) {
            // Extract the nationalId and photo URL
            $nationalId = $nid;
            $photoUrl = $data['data']['photo'];

            // Check if the nationalId is available and valid
            if (!empty($nationalId) && $nationalId !== "Not Available") {
                // Download the photo and store it with nationalId as the filename
                $imageContent = file_get_contents($photoUrl);
                if ($imageContent !== false) {
                    // Save image to the server using nationalId as the filename
                    $imagePath = 'nids/' . $nid . '.jpg';  // Saving image with nationalId as the name
                    file_put_contents($imagePath, $imageContent);
                    $data['data']['photo'] = $imagePath; // Update the response to show local path
                } else {
                    $data['data']['photo'] = 'Error: Unable to download the image.';
                }
            } else {
                $data['data']['photo'] = 'Error: National ID is missing or invalid.';
            }

            // Return modified response
            $data['data']['requestId'] = 'ERROR X';
            $data['author'] = 'https://t.me/xerror.official';
            echo json_encode($data);
        } else {
            echo $content;
        }
    }

    curl_close($curl);
}

// Example usage: Provide NID and DOB
if (isset($_GET['nid']) && isset($_GET['dob'])) {
    $nid = htmlspecialchars($_GET['nid']);
    $dob = htmlspecialchars($_GET['dob']);

    // Call function to output response directly
    getInfo($nid, $dob);
} else {
    echo 'Error: Missing parameters (nid or dob)';
}
?>
