# Restaurant API

NOTES:
<br/>  
**Generate secret key**

I have included a helper command to generate a key for you:

`php artisan jwt:secret`
<br/>  
This will update your `.env` file with something like JWT_SECRET=foobar
<br/>

**Run storage links**

``php artisan storage:link``
<br/>  

**To run Jobs** (Qr Image Generator)

``php artisan queue:work``
