#cd ../ && composer install # TODO: Install PHP stuff
git clone https://github.com/firebrandsolutions/quickcommerce-oc.git . && 
cd vendor && git submodule add https://github.com/firebrandsolutions/quickcommerce.git quickcommerce && 
cd ../ && git submodule add https://github.com/firebrandsolutions/clients-phobulous-theme frontend &&
mkdir -p upload/staging # Create build directory for frontend files
cd frontend && npm install && # Install packages
# Go to the quickcommerce-react lib folder and pull the submodule
# NOTE: New way singe 1.9 git clone --recursive -j8 git://github.com/foo/bar.git
git submodule update --init --recursive && npm install --save react-toggle-display
&& webpack --watch
# TODO: Remove react-toggle-display I don't use it, just in here so I can build