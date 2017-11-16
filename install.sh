git submodule init
git submodule update
cd src/pocketmine/
git checkout aa399a1109d2213e5df84c5390deeda0746c2717
git submodule init
git submodule update
cd ..
cd ..
wget https://jenkins.pmmp.io/job/PHP-7.2-Linux-x86_64/lastSuccessfulBuild/artifact/PHP_Linux-x86_64.tar.gz
tar -xvzf PHP_Linux-x86_64.tar.gz
chmod -R 755 bin/*
chmod 755 proxy.sh
bin/composer install
rm PHP_Linux-x86_64.tar.gz
