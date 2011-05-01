Code Igniter es de esos frameworks con los que he trabajado ya por mucho tiempo, después de usar cosas como Django o Ruby lo siento un poco atrasado. La razón por la cual lo sigo usando es por su simpleza, rapida instalación y arte al momento de codificar. Para hacerme la vida mas facil desarrolle una extensión de la clase CI_Model que permite evitar re-escribir y re-escribir peticiones a la base de datos que se regresan como objetos y que después hay que parsear y después actualizar. Desde mi punto de vista mucho trabajo.

Esta es una clase genérica que permite hacer peticiones del tipo

Esto es un Get del ID 10

$persona = new Persona_model( 10 );
Normalmente harías un


$query = $this->db->get_where('persona', array('id' => $id), 0, 0);
$persona=$query->result();
La diferencia comienza al querer tratar con el objeto. Para Obtener el nombre usaremos

$persona->get( 'nombre' );
En lugar de


$this->db->select('nombre');
$query = $this->db->get('persona');
$persona=$query->result();
Para actualizar usaremos


$persona->set( 'nombre', 'Arturo Jamaica' );
$persona->update();
En lugar de un


$persona = new Persona;
$this->db->where('id', $id);
$this->db->update('persona', $persona);
Si lo notas trabajamos con objetos, no con peticiones recurrentes a la BD. Lo cual nos hace la vida más facil



Leer más en http://walhez.com/?p=12666#ixzz1L4ay6Cce