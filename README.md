# Mesaj Gönderme Servisi – Insider Test Case
## Genel Bakış

Bu proje, her 5 saniyede 2 mesaj gönderen ve mesaj durumlarını API üzerinden görüntüleyebilen bir Laravel 12 uygulamasıdır.
Mesaj durumu takibi için enum yapısı, performans için Redis cache kullanılmıştır.
Repository Pattern ve Service Layer yapılarıyla okunabilir ve sürdürülebilir bir kod tabanı hedeflenmiştir.

#### Teknolojiler

* Laravel 12

* Laravel Queue / Job Worker

* Redis

* MySQL

* Repository Pattern

* Service Layer

* Ön Gereksinimler

* Docker Engine

* Docker Compose

(Opsiyonel) Windows/Mac için Docker Desktop

#### Kurulum ve Çalıştırma

Projeyi localinize indirin:
```bash 
git clone https://github.com/HasanBasri06/Insider.git
```

.env Dosyasını Oluşturun ve Düzenleyin

```bash 
cp .env.example .env
```

Bağımlılıkları indirin:
```bash 
composer install
```


.env içindeki veritabanı, Redis ve WEBHOOK_URL değerlerini güncelleyin.
WEBHOOK_URL için https://webhook.site
 üzerinden bir URL oluşturun ve WEBHOOK_URL alanına ekleyin.

Not: Webhook site içeriğine aşağıdaki JSON yapısını girmeniz gerekir:
```json 
{
  "message": "Accepted",
  "messageId": "67f2f8a8-ea58-4ed0-a6f9-ff217df4d849"
}
```



Docker Compose ile Servisleri Başlatın
```bash 
docker-compose up -d --build
```

Öncelikle Laravel uygulamamızın içine girelim.

```bash 
docker-compose exec -it [container_id] bash
```
Bu sayede laravel uygulamamızın içine girebiliriz.

Veritabanı Migration ve Seeder Çalıştırın

```bash 
php artisan migrate:fresh --seed
```

Queue Worker ve Mesaj Gönderme Job’unu Çalıştırın

İki ayrı terminal açıp sırayla çalıştırın:
```bash 
php artisan queue:work
```
```bash 
php artisan messages:send
```

### API Endpointleri

- Tüm mesajlar:
GET http://localhost:8080/api/messages

- Mesajları limit ile filtreleme:
GET http://localhost:8080/api/messages?limit=4

- Duruma göre filtreleme (örnek: sent):
GET http://localhost:8080/api/messages?status=sent

- Tek mesaj detay (örnek id=1):
GET http://localhost:8080/api/messages/1

### Test

Projede kapsamlı unit ve feature testler bulunmaktadır.
Testleri çalıştırmak için:

```bash 
php artisan test
```

Notlar

WEBHOOK_URL boş bırakılırsa mesaj durumu failed olarak işaretlenir.

Queue worker ve mesaj gönderme job’unun ayrı terminalde sürekli çalışması gerekir.

Hasan Basri Akcıl
Software Developer