Note: this documentation is in progress, it's not ready for use yet!

### Usage

Create a `docker-compose.yml` with the following contents.

```
server:
  image: bluecollardev/quickcommerce-nginx
web:
  image: bluecollardev/quickcommerce-backend
  links:
    - db
  ports:
    - "80:80"
db:
  image: bluecollardev/quickcommerce-db
```

Then run `docker-compose up` and navigate in your browser to

* http://127.0.0.1/ to access the front end, and 
* http://127.0.0.1/admin/ to access the back end.

### Hostname

To set the hostname for OpenCart use the environment variable `VIRTUAL_HOST=foo.bar.com`.

```
server:
  image: bluecollardev/quickcommerce-nginx
web:
  image: bluecollardev/quickcommerce-backend
  links:
    - db
  ports:
    - "80:80"
  environment:
    - VIRTUAL_HOST=foo.bar.com
db:
  image: bluecollardev/quickcommerce-db
```

Then run `docker-compose up --force-recreate` and `foo.bar.com` is used as base URL.

### Backend Login

To change the backend credentials, set the environment variables `SHOP_ADMIN_USER=bob1` and `SHOP_ADMIN_PASSWORD=abc123`.

```
server:
  image: bluecollardev/quickcommerce-nginx
web:
  image: bluecollardev/quickcommerce-backend
  links:
    - db
  ports:
    - "80:80"
  environment:
    - SHOP_ADMIN_USER=bob1
    - SHOP_ADMIN_PASSWORD=abc123
db:
  image: bluecollardev/quickcommerce-db
```

### Plugin Download

To download a plugin on startup, add the environment variable `DOWNLOAD_PLUGIN=https://github.com/foo/bar/archive/master.tar.gz`.

### Usage of OpenCart Extension Installer

The extension installer requires a FTP service in order to upload extensions to your shop, which is not available in this setup. However the following [extension](http://www.opencart.com/index.php?route=extension/extension/info&extension_id=18892) provides a workaround for this shortcoming.

### Development

To use this image for development, use subsequent `docker-compose.yml`.

```
server:
  image: bluecollardev/quickcommerce-nginx
web:
  image: bluecollardev/quickcommerce-backend
  links:
    - db
  ports:
    - "80:80"
  volumes:
    - ./html:/var/www/html
db:
  image: bluecollardev/quickcommerce-db
  ports:
    - "3306:3306"
```

Then run the following commands in order to get the files for the volume:

```
# get container id
docker-compose ps -q web

# copy directory to host and set file permissions
docker cp <container-id>:/var/www/html ./html
chmod -R 0777 html
```