git submodule init
git submodule update
cd src/pocketmine/
git checkout master
cd ..
cd ..
wget https://jenkins.pmmp.io/job/PHP-7.2-Linux-x86_64/lastSuccessfulBuild/artifact/PHP_Linux-x86_64.tar.gz
tar -xvzf PHP_Linux-x86_64.tar.gz
bin/composer install
rm PHP_Linux-x86_64.tar.gz