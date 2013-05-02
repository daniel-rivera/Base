<?php
// clase para la práctica 08
// 2013-05-02 Modificación para GitHub
// DRB

class form {
	// propiedades
	var $articulo;
	var $pagina;
	var $registro; // identificador del registro base
	var $db; // enlace a la DB
	var $datos; // indica si los datos estan disponibles
	var $modo; // indica que es lo que se esta editando o agregando
	var $contenido; // información para la datos
	var $tabla; // tabla base
	var $campos; // contiene las descripciones de los campos
	var $forma; // indica si la forma contiene datos
	
	// metodos
	function form() 
	{
		// obtener los parámetros de acción
		$this->articulo = (empty($_REQUEST['id'])) ? 0 : $_REQUEST['id'];
		$this->pagina = (empty($_REQUEST['p'])) ? 0 : $_REQUEST['p'];
		
		$this->enlaces = Array();
		$this->contenido = Array();
		$this->campos = Array();
		
		// definir la acción a realizar
		$this->modo = 0; // agregar artículo
		if (($this->articulo > 0) && ($this->pagina == 0)) {
			$this->modo = 1; // editar artículo
		} else if (($this->articulo > 0) && ($this->pagina == -1)) {
			$this->modo = 2; // agregar página
		} else if (($this->articulo > 0) && ($this->pagina > 0)) {
			$this->modo = 3; // editar página
		}
		
		// definir la tabla a usar en las consultas
		$this->tabla = 'drb_articulos';
		$this->registro = $this->articulo;
		if ($this->modo >= 2) {
			$this->tabla = 'drb_contenido';
			$this->registro = $this->pagina;
		}
		
		// definir los campos únicos a procesar para articulos o páginas
		$this->campos['ID'] = Array('valor' => $this->registro,'tipo' => 'hidden','limite' => 11);
		
		// definir los campos únicos para páginas
		if ($this->modo > 1) {
			$this->campos['Articulo'] = Array('valor' => $this->articulo,'tipo' => 'text','limite' => 11);
			$this->campos['Fecha'] = Array('valor' => '','tipo' => 'datetime-local','limite' => 0);
			$this->campos['Autor'] = Array('valor' => '','tipo' => 'text','limite' => 11);
		}
		
		$opciones = Array(1 => 'Si',0 => 'No');
		// ahora definir los campos compartidos
		$this->campos['Titulo'] = Array('valor' => '','tipo' => 'text','limite' => 11);
		$this->campos['Contenido'] = Array('valor' => '','tipo' => 'textarea','limite' => 0);
		$this->campos['Imagen'] = Array('valor' => '','tipo' => 'text','limite' => 200);
		$this->campos['Activo'] = Array('valor' => '','tipo' => 'select','limite' => 0,'opciones' => $opciones);
		
		// verificar la existencia de datos en la forma
		$this->forma = isset($_POST['ID']);
		if ($this->forma) {
			// procesar todos los campos definidos
			foreach ($this->campos as $campo => $detalles) {
				$this->campos[$campo]['valor'] = $_POST[$campo];
			}
		}
		
		// conectarse a la Base de datos
		$this->db = new mysqli("localhost", "respues_sii", "iti2013", "respues_sii");
		$this->datos = $this->db->connect_errno == 0;
		
		if ($this->datos) {
			// establecer el charset
			$this->db->set_charset("utf8");
			
			if (($this->modo == 0) || ($this->modo == 2)) {
				// agregar un registro con los datos básicos
				$valores = ' (Activo) VALUES (1)';
				if ($this->modo == 2) {
					$valores = ' (Articulo,Fecha,Autor,Activo) VALUES (' . $this->articulo . ',NOW(),1,1)';
				}
				
				$query = 'INSERT INTO ' . $this->tabla . $valores;
				if ($this->db->query($query)) {
					// reasignar el ID de resultado
					if ($this->modo == 0) {
						$this->articulo = $this->db->insert_id;
					} else {
						$this->pagina = $this->db->insert_id;
					}
				} else {
					// ocurrio un error al insertar
					$this->datos = FALSE;
				}
			} else if (($this->modo == 1) || ($this->modo == 3)) {
				// si la forma ya tiene datos almacenarlos,
				// en caso contrario se recuperan de la DB 
				if ($this->forma) {
					// preparar la consulta para actualizar
					$campos = '';
					
					foreach ($this->campos as $campo => $detalles) {
						if (! empty($campos)) {
							$campos .= ', ';
						}
						if ($detalles['tipo'] != 'hidden') {
							// asignar el par campo=valor
							$campos .= $campo . '=\'' . $this->db->real_escape_string($detalles['valor']) . '\'';
						}
					}
					
					$query = 'UPDATE ' . $this->tabla . ' SET ' . $campos .
							 ' WHERE ID=' . $this->registro;
					
					$this->db->query($query);
				} else {
					// localizar el articulo indicado con el ID
					$query = 'SELECT * FROM ' . $this->tabla . ' WHERE ID = ' . $this->registro;
					
					if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
						// almacenar todos los datos
						$row = $result->fetch_assoc();
					
						foreach ($row as $campo => $valor) {
							$this->campos[$campo]['valor'] = stripslashes($valor);
						}
						
						// liberar el resultado
						$result->close();
					} else {
						$this->datos = FALSE;
					}	
				}			
			}
		
			// definir los enlaces de navegación
			if ($this->datos) {
				if ($this->modo <= 1) {
					$this->enlaces[] = Array('enlace' => 'index.php?id=' . $this->articulo,
								  'etiqueta' => 'View article');
				} else {
					$this->enlaces[] = Array('enlace' => 'index.php?id=' . $this->articulo . '&p=' . $this->pagina,
								  'etiqueta' => 'View page');
				}				
			}
		} else {
			// no se puede conectar a la DB
			$this->articulo = 0;
		}

		// asignar valores por defecto si no hay DB
		if (! $this->datos) {
			$this->contenido['titulo'] = 'Error';
			$this->contenido['contenido'] = 'Realmente lo sentimos mucho, pero ha ocurrido un error 
											 al procesar la petición.';
			$this->contenido['imagen'] = 'error.jpg';
			$this->enlaces[] = Array('enlace' => 'index.php','etiqueta' => 'Home');
		}
	}
	
	function close()
	{
		// terminar los objetos utilizados
		$this->db->close();
	}
	
	function titulo()
	{
		if ($this->modo <= 1) {
			echo "Edit article";
		} else {
			echo "Edit page";
		}
	}
	
	function navegacion()
	{
		// escribir los enlaces
		foreach ($this->enlaces as $enlace) {
			echo '<li><a href="' . $enlace['enlace'] . '">' . $enlace['etiqueta'] . '</a></li>';
		}
	}
	
	function contenido()
	{
		if (! $this->datos) {
			// colocar los datos de error
			echo '<aside id="featured" class="body">
				 <article>';
		
			if ($this->contenido['imagen'] != '-') {
				echo '<figure>
						<img src="' . $this->contenido['imagen'] . '" alt="" />
					</figure>';
			}
			
			echo '<hgroup>
					<h2>' . $this->contenido['titulo'] . '</h2>
				</hgroup>' . $this->contenido['contenido'] . '
				</article></aside><!-- /#error -->';
		} else {
			// colocar la forma de edición de contenido
			echo '<section id="content" class="body">
					<article>';
			
			echo '<hgroup>
					<h2>Content fields</h2>
				</hgroup>';
			
			// colocar la forma con los datos del script ajustados a la acción adecuada
			$parametros = 'id=' . $this->articulo;
			if ($this->modo > 1) {
				$parametros .= '&p=' . $this->pagina;
			}
			
			echo '<form id="data" action="edit.php?' . $parametros . '" method="post">';
			
			// procesar todos los campos definidos
			foreach ($this->campos as $campo => $detalles) {
				if ($detalles['tipo'] == 'hidden') {
					echo '
						<input type="hidden" name="' . $campo . '" value="' . $detalles['valor'] . '"><br />';
				} else if (($detalles['tipo'] == 'text') || ($detalles['tipo'] == 'datetime-local')) {
					echo '
							' . $campo . ':<br /><input type="' . $detalles['tipo'] . '" name="' . $campo . '" value="' . stripslashes($detalles['valor']) . '"><br />';
				
				} else if ($detalles['tipo'] == 'select') {
					echo '
							' . $campo . ':<br /><select name="' . $campo . '">';
					foreach ($detalles['opciones'] as $valor => $etiqueta) {
						echo '
							<option value="' . $valor . '">' . $etiqueta . '</option>';
					}
					echo '</select>';
				} else {
					echo '
							' . $campo . ':<br /><textarea rows="15" name="' . $campo . '">' . stripslashes($detalles['valor']) . '</textarea><br />';
				}
			}
				
			echo '
				<input id="submit" type="submit" value="Submit">			
			</form></article></section><!-- /#form -->';
			
		}
	}
}

// crear una instancia de la clase
$form = new form();

?>