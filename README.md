```bash
git clone git@github.com:Rasskar/test-excel-parser.git
```
```bash
cd test-excel-parser
```
```bash
./sail -f docker-compose.yml -f docker-compose.dev.yml up -d
```
```bash
./sail composer install
```
```bash
cp .env.example .env
```
```bash
./sail artisan key:generate
```
```bash
./sail artisan migrate
```
```bash
./sail artisan migrate
```
```bash
./sail artisan l5-swagger:generate
```
```bash
http://localhost
```
```bash
http://localhost/api/documentation/
```
```bash
http://localhost/horizon/dashboard
```
