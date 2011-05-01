/* EX_Model.php */
class EX_Model extends CI_Model{
        
        protected       $db_table  = NULL; // Tabla
        protected       $fields = array( 'id' => NULL ); // Arreglo para guardar Campos y Contenidos
        private         $id = NULL;// ID del objeto
        
        public function __construct()
        {
                parent::__construct();
                
                /*
                * Sirve para quitar el _Model del modelo standard y usar solo el_nombre_de_la_clase en la tabla
                * ejemplo: La clase Persona_model se vera como 'persona en la BD'
                */

                $this->db_table = strtolower( str_replace( '_model', '', get_class( $this ) ) );
        }
        
        protected function set_params( $fields = NULL, $db_table = NULL )
        {
                if( ! $fields )
                {
                        exit( 'Missing fields' );
                }
                
                // $db_table sirve para usar otro nombre a la tabla si no queremos usar el mismo de la clase
                if( $db_table )
                {
                        $this->db_table = $db_table;
                }
                
                $fields = array_merge( $this->fields, $fields );
                
                foreach( $fields as $k => $v )
                {
                        $this->fields[$k] = $v;
                }
                
                
        }
        
        public function set( $field = NULL, $value = NULL )
        {
                if( array_key_exists( $field, $this->fields ) )
                {
                        if( trim( $value ) == '' )
                        {
                                $value = NULL;
                        }
                        
                        $this->fields[ $field ] = $value;
                        return TRUE;
                }
                else
                {
                        exit( $field . ' is not a variable to set.' );
                }
        }
                
        public function get( $field )
        {
                if( array_key_exists( $field, $this->fields ) )
                {
                        return $this->fields[ $field ];
                }
                else
                {
                        exit( $field . ' is not a variable to get' );
                }               
        }
        
        public function list_all( $params = NULL )
        {
                
                if( $params['where'] )
                {
                        foreach( $params['where'] as $field => $value )
                        {
                                $this->db->where( $field, $value );
                        }
                }
                
                $this->db->from( $this->db_table );
                
                if( $params['limit'] )
                {
                        if( $params['offset'] )
                        {
                                $this->db->limit( $params['limit'], $params['offset'] );
                        }
                        else
                        {
                                $this->db->limit( $params['limit'] );
                        }
                }
                
                
                $query = $this->db->get();
                $item_list = array();
                
                foreach( $query->result() as $row )
                {
                        $item = new $this;
                        $item->populate( $row );
                        $item_list[] = $item;
                }
                
                return $item_list;
                
        }
        
        public function count( $where = NULL )
        {
                if( $where && is_array( $where ) )
                {
                        foreach( $where as $field => $value )
                        {
                                $this->db->where( $field, $value );
                        }
                }
                
                return $this->db->count_all_results( $this->db_table );         
        }
        

        public function populate( $row )
        {               
                
                $this->id = $row->id;
                foreach( $this->fields as $k => $v )
                {
                        $this->set( $k, $row->$k );
                }
        }
        
        public function add()
        {
                // Cambiamos los campos sin usar a null
                foreach( $this->fields as $k => $v )
                {
                        if( trim( $v ) == '' )
                        {
                                $this->fields[$k] = NULL;
                        }
                }
                
                $insert_array = array();
                
                $insert_array = array_merge( $insert_array, $this->fields );
                
                if( $this->db->insert( $this->db_table, $insert_array ) )
                {
                        $this->set( 'id', $this->db->insert_id() );
                        return TRUE;
                }
                else
                {
                        return FALSE;
                }
        }
        
        public function update()
        {
                if( ! $this->id )
                {
                        return FALSE;
                }

                foreach( $this->fields as $k => $v )
                {
                        if( trim( $v ) == '' )
                        {
                                $this->fields[$k] = NULL;
                        }
                }
                                
                $update_array = array();

                $update_array = array_merge( $update_array, $this->fields );
                $this->db->where( 'id', $this->get( 'id' ) );
                return $this->db->update( $this->db_table, $update_array );
                
        }
        
        public function delete( $id = NULL )
        {
                if( $id != NULL )
                {
                        $this->db->delete( $this->db_table, array( 'id' , $id ) );
                        return ( $this->db->affected_rows() > 0 );
                        
                }
                else
                {
                        return FALSE;
                }
        }
        
}

/* User_model.php */
class Persona_model extends EX_Model{

        /*
         * Nuevo constructor para aprovechar las ventajas de la nueva clase
        */
        public function __construct( $id = NULL )
        {
                $fields = array();
                $fields['nombre']         =       NULL;
                $fields['apellido']        =       NULL;
                
                parent::set_params( $fields );
                parent::__construct();
                
                if( $id )
                {
                        $this->db->where( 'id', $id );
                        $query = $this->db->get( $this->db_table );
                        
                        if( $query->num_rows() != 0 )
                        {
                                $row = $query->row();
                                $this->populate( $row );
                        }
                }
        }
}