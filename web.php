<?php
// clase para la práctica 08
// 2013-05-02 Modificacion para GitHub
// EHR

class web {
	// propiedades
	var $articulo;
	var $pagina;
	var $enlaces;
	var $db; // enlace a la DB
	var $datos; // indica si los datos estan disponibles
	var $contenido; // información para la página
	var $principal; // indica si es la página principal
	
	// metodos
	function web() 
	{
		// obtener los parámetros de acción
		$this->articulo = (empty($_REQUEST['id'])) ? 0 : $_REQUEST['id'];
		$this->pagina = (empty($_REQUEST['p'])) ? 0 : $_REQUEST['p'];
		$this->enlaces = Array();
		$this->contenido = Array();
		
		$this->principal = ($this->articulo == 0) || ($this->pagina == 0);
		
		// conectarse a la Base de datos
		$this->db = new mysqli("localhost", "respues_sii", "iti2013", "respues_sii");
		$this->datos = $this->db->connect_errno == 0;
		
		if ($this->datos) {
			// establecer el charset
			$this->db->set_charset("utf8");
			
			if ($this->principal) {
				// localizar el articulo indicado con el ID o el primero disponible si no hay indicado un ID
				$where = 'WHERE (Activo = 1)';
				if ($this->articulo != 0) {
					$where .= ' AND (ID = ' . $this->articulo . ') ';
				}				
				$query = 'SELECT * FROM drb_articulos ' . $where . 'ORDER BY ID LIMIT 1';
				
				if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
					// almacenar los datos
					$row = $result->fetch_object();
					$this->contenido['titulo'] = stripslashes($row->Titulo);
					$this->contenido['contenido'] = stripslashes($row->Contenido);
					$this->contenido['imagen'] = $row->Imagen;
					$this->contenido['paginas'] = Array();
					
					// reasignar el ID por si no había ninguno especificado
					$this->articulo = $row->ID;
					
					// liberar el resultado
					$result->close();
				} else {
					$this->datos = FALSE;
				}
				
				// recuperar los extractos de la información de las páginas
				if ($this->datos) {
					$query = 'SELECT a.ID, a.Fecha, b.Nombre, a.Titulo, a.Contenido 
							 FROM drb_contenido a, drb_autores b WHERE (a.Activo = 1) AND (a.Articulo = ' . 
							 $this->articulo . ') AND (b.ID = a.Autor) ORDER BY a.ID';
				
					if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
						// almacenar los datos
						$indice = 1;
						while ($row = $result->fetch_object()) {
							$pagina = Array();
							$pagina['articulo'] = $this->articulo;
							$pagina['pagina'] = $row->ID;
							$pagina['fecha'] = $row->Fecha;
							$pagina['autor'] = $row->Nombre;
							$pagina['titulo'] = '#' . $indice . ') ' . stripslashes($row->Titulo);
							
							// obtener únicamente la primera sección del contenido, utilizar el Tag <middle>
							$tagpos = strpos($row->Contenido,'<middle>');
							if ($tagpos < 1) {
								// copiar los primeros 150 caracteres
								$tagpos = 151;
							}
							$pagina['contenido'] = strip_tags(substr(stripslashes($row->Contenido),0,$tagpos - 1));
							
							$this->contenido['paginas'][] = $pagina;
							$indice ++;
						}
						// liberar el resultado
						$result->close();
					}
				}
			} else {
				// localizar la página especifica para el articulo
				// primero localizar el articulo 
				$query = 'SELECT Titulo FROM drb_articulos WHERE (Activo = 1) AND 
						 (ID = ' . $this->articulo . ') ORDER BY ID LIMIT 1';
				
				if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
					// almacenar los datos
					$row = $result->fetch_object();
					$this->contenido['titulo'] = stripslashes($row->Titulo);
					
					// liberar el resultado
					$result->close();
				} else {
					$this->datos = FALSE;
				}
				
				// recuperar el contenido de la página
				if ($this->datos) {
					$query = 'SELECT a.Fecha, b.Nombre, a.Titulo, a.Contenido, a.Imagen 
							 FROM drb_contenido a, drb_autores b WHERE (a.Activo = 1) AND 
							 (a.ID = ' . $this->pagina . ') AND 
							 (a.Articulo = ' . $this->articulo . ') AND 
							 (b.ID = a.Autor) ORDER BY a.ID';
				
					if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
						// almacenar los datos
						$row = $result->fetch_object();
						
						$this->contenido['articulo'] = stripslashes($row->Titulo);
						$this->contenido['fecha'] = $row->Fecha;
						$this->contenido['autor'] = $row->Nombre;
						$this->contenido['contenido'] = stripslashes($row->Contenido);
						$this->contenido['imagen'] = $row->Imagen;
						
						// liberar el resultado
						$result->close();
					}
				}
			}
		
			// definir enlaces disponibles para las páginas
			if ($this->datos) {
				// definir el enlace por defecto
				$this->enlaces[] = Array('enlace' => 'index.php?id=' . $this->articulo,
								  'etiqueta' => 'Home','activo' => $this->pagina == 0 ? TRUE : FALSE);
				
				$query = 'SELECT ID, Titulo	FROM drb_contenido WHERE (Activo = 1) AND (Articulo = ' . 
							 $this->articulo . ') ORDER BY ID';
				
				if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
					// procesar los enlaces
					while ($row = $result->fetch_object()) {
						$enlace = 'index.php?id=' . $this->articulo . '&p=' . $row->ID;
						$this->enlaces[] = Array('enlace' => $enlace,'etiqueta' => strip_tags(stripslashes($row->Titulo)),
												 'activo' => $this->pagina == $row->ID ? TRUE : FALSE);
					}
					// liberar el resultado
					$result->close();
				}				
			}
			
			// localizar los artículos activos que sean diferentes del actual
			$query = 'SELECT ID, Titulo FROM drb_articulos WHERE (Activo = 1) AND (ID <> ' . $this->articulo . ') ORDER BY ID';	
			if (($result = $this->db->query($query)) && ($result->num_rows > 0)) {
				// procesar los datos
				while ($row = $result->fetch_object()) {
					$this->contenido['articulos'][] = Array('id' => $row->ID,'titulo' => strip_tags(stripslashes($row->Titulo)));
				}
				// liberar el resultado
				$result->close();
			}			
		} else {
			// no se puede conectar a la DB
			$this->articulo = 0;
		}

		// asignar valores por defecto si no hay DB
		if (! $this->datos) {
			$this->principal = TRUE;
			$this->contenido['titulo'] = 'Error';
			$this->contenido['contenido'] = 'Realmente lo sentimos mucho pero el artículo indicado no existe, 
											 no tiene contenido o no se encuentra disponible.';
			$this->contenido['imagen'] = 'error.jpg';
			$this->enlaces[] = Array('enlace' => 'index.php','etiqueta' => 'Home','activo' => $this->articulo == 0 ? TRUE : FALSE);
		}
	}
	
	function close()
	{
		// terminar los objetos utilizados
		$this->db->close();
	}
	
	function titulo($navegador)
	{
		if ($navegador) {
			echo $this->enlaces[$this->pagina]['etiqueta'];
		} else {
			echo $this->contenido['titulo'];
		}
	}
	
	function navegacion()
	{
		// escribir los enlaces
		foreach ($this->enlaces as $enlace) {
			if ($enlace['activo']) {
				echo '<li class="active"><a href="#">' . $enlace['etiqueta'] . '</a></li>';	
			} else {
				echo '<li><a href="' . $enlace['enlace'] . '">' . $enlace['etiqueta'] . '</a></li>';	
			}
		}
	}
	
	function featured()
	{
		// colocar el extracto resaltado únicamente en la página principal
		if ($this->principal) {
			echo '<aside id="featured" class="body">
				 <article>';
		
			if ($this->contenido['imagen'] != '-') {
				echo '<figure>
						<img src="' . $this->contenido['imagen'] . '" alt="" />
					</figure>';
			}
			
			echo '<hgroup>
					<h2>Featured Article</h2>
					<h3><a href="#">' .  strip_tags($this->contenido['titulo']) . '</a></h3>
				</hgroup>' . $this->contenido['contenido'] . '
				<!-- edit links -->
				<footer>
					<h3>Options</h3>					
					<ul>
						<li><a href="edit.php?id=' . $this->articulo . '" rel="external">Edit this article</a></li>
						<li><a href="edit.php?id=' . $this->articulo . '&p=-1" rel="external">Add new page in this article</a></li>
					</ul>
				</footer>
				</article></aside><!-- /#featured -->';
		}
	}
								  
	function contenido()
	{
		// colocar el contenido de la página
		if ($this->principal && ! empty($this->contenido['paginas'])) {
			// colocar los extractos de las páginas
			echo '<section id="content" class="body">
				  <ol id="posts-list" class="hfeed">';
	
			foreach ($this->contenido['paginas'] as $pagina) {
				echo '<li><article class="hentry">	
						<header>
						<h2 class="entry-title"><a href="index.php?id=' . $pagina['articulo'] . '&p=' . $pagina['pagina'] . '" rel="bookmark" 
						title="Permalink to this page">' . $pagina['titulo'] . '</a></h2>
						</header>
						<footer class="post-info">
						<abbr class="published" title="' . $pagina['fecha'] . '"><!-- YYYYMMDDThh:mm:ss+ZZZZ -->
							' . date('jS \of F Y', strtotime($pagina['fecha'])) . '
						</abbr>
						<address class="vcard author">
							By <a class="url fn" href="#">' . $pagina['autor'] . '</a>
						</address>
						</footer><!-- /.post-info -->
						<div class="entry-content">
							<p>' . $pagina['contenido'] . '</p>
						</div><!-- /.entry-content -->
						</article></li>';
			}
			
			echo '</ol><!-- /#posts-list -->
				 </section><!-- /#content -->';
		}
		
		if (! $this->principal) {
			echo '<section id="content" class="body">
					<article>';
			
			if ($this->contenido['imagen'] != '-') {
				echo '<figure>
						<img src="' . $this->contenido['imagen'] . '" alt="" />
					</figure>';
			}
			
			echo '<hgroup>
					<h2>' . $this->contenido['articulo'] . '</h2>
					<h3 class="published" title="' . $this->contenido['fecha'] . '"><!-- YYYYMMDDThh:mm:ss+ZZZZ -->
							' . date('jS \of F Y', strtotime($this->contenido['fecha'])) . '
					<address class="vcard author">
						By <a class="url fn" href="#">' . $this->contenido['autor'] . '</a>
					</address>
					</h3>
				</hgroup>
				' . $this->contenido['contenido'] . '
				<!-- edit links -->
				<footer>
					<h3>Options</h3>					
					<ul>
						<li><a href="edit.php?id=' . $this->articulo . '&p=' . $this->pagina . '" rel="external">Edit this page</a></li>
						<li><a href="edit.php?id=' . $this->articulo . '&p=-1" rel="external">Add new page in this article</a></li>
					</ul>
				</footer>
				</article></section><!-- /#content -->';
		}
	}
	
	function articulos()
	{
		// colocar los enlaces a otros artículos
		echo '<section id="other" class="body">
			 <h2>Other articles</h2>
			 <ul>';
			 
		foreach ($this->contenido['articulos'] as $articulo) {
			echo '
				<li><a href="index.php?id=' . $articulo['id'] . '	" rel="external">' . $articulo['titulo'] . '</a></li>';
		}
		
		// agregar un enlace para agregar artículos nuevos
		echo '
			 <li><a href="edit.php?id=-1" rel="external">Add a new article ...</a></li>';
			
		echo '</ul>
			 </section><!-- /#other articles -->';
	}  
}

// crear una instancia de la clase
$web = new web();

?>