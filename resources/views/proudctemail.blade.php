<!DOCTYPE html>
<html>
<head>
    <title>Product Detail</title>
</head>
<body>
    <h1>{{ $mailData['title'] }}</h1>
    <p>{{ $mailData['product_name'] }}</p>
    <p>{{ $mailData['product_price'] }}</p>
    <p>{{ $mailData['phone_number'] }}</p>
    <p>{{ $mailData['product_description'] }}</p>
    <p><img src="http://productproject.test/{{ $mailData['product_image'] }}">
    
   
    
   
</body>
</html>