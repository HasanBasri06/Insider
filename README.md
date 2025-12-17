# Mesaj Gönderme Servisi – Insider Test Case
## Genel Bakış

Bu proje, her 5 saniyede 2 mesaj gönderen ve mesaj durumlarını API üzerinden görüntüleyebilen bir Laravel 12 uygulamasıdır.
Mesaj durumu takibi için enum yapısı kullandım.
Repository Pattern ve Service Layer yapılarıyla okunabilir ve sürdürülebilir bir kod tabanı hedefledim.

Kodları PSR-12 standartlarına uygun hale getirmek için Laravel’in Pint paketini kullandım. Insider için bu standartların önemli olduğunu bildiğimden, Pint’i tercih ettim.

#### Teknolojiler

* Laravel 12

* Laravel Queue / Job Worker

* Redis

* MySQL

* Repository Pattern

* Service Layer

* Docker

* PSR12

Normal kullanımda PHPDoc satırlarıda kullanırım. Aşağıdaki kod örneğinde olduğu gibi:
```php
/**
 * @param int $id // burada olduğu gibi
 * @return ?Message
 */
public function getMessageById(int $id): ?Message
{
    return $this
        ->messageRepository
        ->findByIdWithUser($id);
}
```
* Yaptığım araştırmalar sonucunda, scope içerisinde type-safe kullanılıyorsa, PSR-12 standartlarına göre @param etiketinin gereksiz olduğu sonucuna vardım ve bu gereksiz açıklamaları Pint aracıyla kaldırdım.

* Ayrıca, kodlarımda tekrar kullanılabilirliği artırmak amacıyla çeşitli yapılar oluşturdum. Örneğin; API çıktılarında oluşabilecek hata durumlarında, client’a hata mesajı döndürme işlemini her seferinde tekrarlamak yerine, bu işlemi ResponseTrait.php içerisinde merkezi bir şekilde topladım.

```php 
trait ResponseTrait
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function error(string $message, int $code = 400): Response
    {
        return response([
            'message' => $message,
            'code' => $code,
        ], $code);
    }
}
```
Bu sayede tek bir standartımız olmuş oldu.

* Veriyi client tarafında tutarlı ve düzenli şekilde sunabilmek amacıyla Laravel’in API Resources yapısını kullandım. Bu sayede API çıktılarının standartlaşmasını ve okunabilirliğini sağladım.
<br />

* Projeyi GitHub’da oluşturduğum ilk andan itibaren, her değişikliği düzenli commit mesajlarıyla belgeleyerek çalışma disiplinimi sürdürdüm.


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

Ardından Laravel uygulamamızın içine girelim.

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