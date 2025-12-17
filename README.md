# Mesaj Gönderme Servisi  
### Insider Test Case

---

## Genel Bakış  
Bu proje, kullanıcıya her 5 saniyede 2 adet mesaj göndermek için tasarlanmıştır. Ayrıca mesajların durumlarını API endpoint’leri aracılığıyla görüntüleyebilirsiniz.

Projede hem tekrar kullanılabilirlik hem de okunabilirlik açısından Repository Pattern kullandım. Mesaj durumlarını kontrol etmeyi kolaylaştırmak için enum yapısı kullandım; bu enum hem veritabanında hem de kod tarafında kullanıldı. Enum değerlerinin migration sırasında otomatik oluşması için gerekli yapı eklendi.

Her bir scope içindeki değerleri type-safe olarak tanımladım. Methodların dönüş tiplerini belirleyerek daha güvenli bir kod ortamı sağladım.  
Örnek bir method tanımı:  

```php
/**
 * @param int $limit
 * @param string|null $filter
 * @return ?Collection
 */
public function getAllMessages(int $limit, string|null $filter): ?Collection;
```

Insider bünyesinde bu tarz yazım standartlarına önem verildiği için test case’imi de bu şekilde kodladım.

API sonuçlarında oluşabilecek hata ya da boş sonuç döndürme durumları için ResponseTrait oluşturdum ve tekrar kullanılabilir şekilde ayarladım. Controller içerisinde bunu kullandım, proje büyüdüğünde de tekrar tekrar kullanılabilir.

Gönderilen mesajların durum ve içerik bilgilerini performans ve hızlı erişim amacıyla Redis önbellek sistemine kaydettim. Böylece, mesajlara ait verilere veritabanına her seferinde sorgu yapmak yerine Redis üzerinden çok daha hızlı erişilebiliyor. Bu yaklaşım, sistemin ölçeklenebilirliğini artırırken, aynı zamanda mesaj durumlarının gerçek zamanlı olarak takip edilmesini kolaylaştırıyor. Özellikle yüksek trafik altında, Redis sayesinde mesaj gönderme ve durum takibi işlemleri daha verimli ve hızlı bir şekilde yönetilebiliyor.

## Kullandığım Teknolojiler

Laravel 12
Laravel Queue / Job Worker
Repository Pattern
Service Layer

## Kurulum ve Gereksinimler

```bash
cp .env.example .env    
```

Ardından veritabanı bilgilerinizi kendinize göre ayarlayın. Mesaj gönderme job’unun çalışması ve geri dönüşünde oluşacak messageId değerini alabilmek için https://webhook.site/
 adresinden bir URL oluşturup, .env dosyasındaki <b>WEBHOOK_URL</b> alanına yerleştirmeniz gerekmektedir.

Webhook URL’si oluşturduktan sonra, sağ üstte bulunan Edit bölümüne gidip, Content kısmına aşağıdaki JSON yapısını girmeniz gerekiyor:
```json 
{
    "message": "Accepted",
    "messageId": "67f2f8a8-ea58-4ed0-a6f9-ff217df4d849"
}
değerini girmelisinz
```

Veritabanı tasarımı migration dosyaları ile oluşturulmuştur fakat test kullanıcıları ve test mesajların oluşması için yazdığım seeder'ı çalıştırmalısınız. Eğer mesajlar veritabanına yazılmazsa sistem çalışmaz.

Seeder çalıştırmak için:

```php
php artisan migrate:fresh --seed
```

## Nasıl Çalıştırılır?
Mesajların queue ile çalışması için iki farklı artisan komutu çalıştırmanız gerekir:

```php 
php artisan queue:work
```
```php 
php artisan messages:send
```

Bu iki komutu iki ayrı terminalde çalıştırmanızı öneririm. Queue servisi, her 5 saniyede 2 defa veritabanında gönderilmemiş mesajları gönderir. Gönderilen mesaj başarıyla işlenirse, veritabanındaki enum değeri sent olarak güncellenir.

<i>Not: Eğer <b>WEBHOOK_URL</b> boş bırakılırsa mesajın durumu failed olarak işaretlenir.</i>

## API Endpointleri
Tüm işlemlerin sonucunda oluşan mesajların API çıktısını aşağıdaki endpoint’lerden görüntüleyebilirsiniz:

- Tüm mesajları görüntülemek için:
    GET http://127.0.0.1:8000/api/messages

- Mesajları limit parametresi ile filtrelemek için:
    GET http://127.0.0.1:8000/api/messages?limit=4

- Mesajları status parametresi ile filtrelemek için (örneğin status=sent):
    GET http://127.0.0.1:8000/api/messages?status=sent

- Tek bir mesajın detayını görüntülemek için (örneğin id=1):
    GET http://127.0.0.1:8000/api/messages/1


## Test

Projede, mesaj gönderme servisinin ve job'un doğru çalıştığını garanti altına almak için kapsamlı Feature Testler yazılmıştır.

Testleri çalıştırmak için 
```php 
php artisan test
```
kullanmalısınız.

# Proje Test Rehberi

Bu projede hem **Unit Test** hem de **Integration Test** yazılması beklenmektedir. Testler, kodun doğru çalıştığını garanti altına almak ve olası hataları erken tespit etmek amacıyla önemlidir.

---

## Projede Yazılmış Testler

### Unit Test Örneği: `MessageServiceTest`

Bu test sınıfı, `MessageService` içerisinde bulunan metodların doğru çalışıp çalışmadığını bağımsız olarak kontrol eder. Örnek testler:

- Mesaj içeriği çok uzun olduğunda hata mesajının doğru yazılması.
- Mesaj gönderildiğinde repository üzerinden `markAsSent` metodunun çağrılması.
- Başarısız durumlarda hata mesajının doğru şekilde kaydedilmesi.
- Repository metodlarının çağrılması ve doğru veri döndürmesi.

### Test Çalıştırma

Projede PHPUnit kullanılarak testler çalıştırılır. Testleri çalıştırmak için terminalden aşağıdaki komutu kullanabilirsiniz:

```bash
php artisan test
```
<hr />
Hasan Basri Akcıl<br />
Software Developer
