<?php
echo "<h2>Upload Test</h2>";

echo "<h3>1. Check Folder Exists:</h3>";
echo file_exists('images/products/') ? "✅ YES" : "❌ NO - Create it!";

echo "<h3>2. Check Folder Writable:</h3>";
echo is_writable('images/products/') ? "✅ YES" : "❌ NO - Fix permissions!";

echo "<h3>3. Current Directory:</h3>";
echo getcwd();

echo "<h3>4. List Files in products folder:</h3>";
$files = scandir('images/products/');
print_r($files);

echo "<h3>5. Upload Test Form:</h3>";
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file">
    <button type="submit" name="test_upload">Test Upload</button>
</form>

<?php
if (isset($_POST['test_upload']) && isset($_FILES['test_file'])) {
    echo "<h3>6. Upload Result:</h3>";
    print_r($_FILES['test_file']);
    
    if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['test_file']['tmp_name'];
        $name = 'test_' . time() . '.jpg';
        $dest = 'images/products/' . $name;
        
        if (move_uploaded_file($tmp, $dest)) {
            echo "<p style='color: green;'>✅ Upload SUCCESS! File: $dest</p>";
            echo "<img src='$dest' style='max-width: 200px;'>";
        } else {
            echo "<p style='color: red;'>❌ Upload FAILED!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error: " . $_FILES['test_file']['error'] . "</p>";
    }
}
?>