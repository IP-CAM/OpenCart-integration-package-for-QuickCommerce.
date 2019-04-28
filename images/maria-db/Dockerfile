FROM mariadb:latest

RUN { \
        echo "[mysqld]"; \
        echo "datadir=/var/lib/db-data"; \
    } > /etc/mysql/conf.d/my.cnf

ADD data.tar.gz /var/lib/db-data

#EXPOSE 3306