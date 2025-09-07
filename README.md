
# TITAN ORM: Un ORM para PHP


¡Bienvenido a TITAN ORM! Este es un Object-Relational Mapper (ORM) en desarrollo para PHP, diseñado para facilitar la interacción con tu base de datos de una manera intuitiva y poderosa.

Nuestro objetivo es permitirte definir tus entidades de base de datos como simples clases PHP usando atributos (Attributes). Esto no solo simplifica tu código, sino que también lo hace más legible y mantenible.

### Características Clave

- **Mapeo Declarativo:** Define tus entidades de base de datos usando atributos directamente en las propiedades de tus clases. Olvídate de los archivos de configuración XML o YAML.

- **Soporte para Múltiples Motores de Base de Datos:** Actualmente, TITAN ORM es compatible con PostgreSQL y MySQL.

- **Migraciones Automáticas:** Genera y ejecuta migraciones de base de datos directamente desde tus clases de entidad.

### Un Vistazo Rápido: La Entidad ``City``
Aquí tienes un ejemplo de cómo se ve una entidad en TITAN ORM, utilizando la clase ``City.``

```php
<?php

namespace Cabez\TitanOrm\Models;

use Cabez\TitanOrm\Kernel\Database\Migrations\Migration;
use Cabez\TitanOrm\Kernel\Interfaces\TypeData;
use Cabez\TitanOrm\Kernel\Database\Attributes\Column;
use Cabez\TitanOrm\Kernel\Database\Attributes\Entity;
use Cabez\TitanOrm\Kernel\Database\Attributes\PrimaryKey;
use Cabez\TitanOrm\Kernel\Database\Attributes\Relations\ManyToMany;

#[Entity(name: "citie", schema: "cities")]
class City extends Migration {

    #[PrimaryKey(name: "id", autoIncrement: true)]
    public int $id;

    #[Column(TypeData::VARCHAR, name: "name", length: 100, order: 2)]
    public string $name;

    #[Column(TypeData::VARCHAR, name: "country", length: 100, order: 3)]
    public string $country;

    #[ManyToMany(targetEntity: Person::class, nameRelation: "id")]
    public Person $person;

}
```
Como puedes ver, los atributos como `#[Entity]`, `#[PrimaryKey]`, `#[Column]` y `#[ManyToMany]` definen la estructura de tu tabla y las relaciones con otras entidades.

#### Cómo Empezar
1. **Clona el Proyecto:**
2. **Configura tu Base de Datos:** Asegúrate de que tu base de datos esté creada. Por ejemplo, `test_orm`

```hthacess
DB_DRIVER="pgsql"
DB_HOST="localhost"
DB_PORT="5432"
DB_NAME="test_orm" 
DB_USER="postgres"
DB_PASSWORD="123456"
DB_CHARSET="utf8"
```

#### ¡Contribuir es Bienvenido!
TITAN ORM es un proyecto en desarrollo. ¡Cualquier ayuda es bienvenida! Si te interesa el desarrollo de ORMs o simplemente quieres contribuir, no dudes en revisar el código, abrir `issues` o enviar `pull requests`. Tu colaboración nos ayudará a hacer de TITAN ORM una herramienta aún mejor.


