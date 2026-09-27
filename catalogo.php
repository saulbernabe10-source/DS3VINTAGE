<?php
require_once __DIR__ . '/includes/funciones.php';
$rutaBase = '';
$tituloPagina = 'Catálogo';

$f = filtrosCatalogoDesdeQuery();
$whereSql = $f['where'];
$params = $f['params'];
$ordenSql = $f['orden'];
$q = $f['q'];
$categoriasSel = $f['categoriasSel'];
$marcasSel = $f['marcasSel'];
$tallasSel = $f['tallasSel'];
$precioMin = $f['precioMin'];
$precioMax = $f['precioMax'];
$disponibilidadSel = $f['disponibilidad'];
$orden = $f['ordenSel'];
$pagina = 1;
$porPagina = 18;

$info = paginar('productos p', $whereSql, $params, $pagina, $porPagina);

$stmt = db()->prepare("
    SELECT p.*, m.nombre AS marca_nombre,
        (SELECT url FROM producto_fotos WHERE producto_id = p.id ORDER BY orden LIMIT 1) AS foto
    FROM productos p LEFT JOIN marcas m ON m.id = p.marca_id
    WHERE {$whereSql}
    ORDER BY {$ordenSql}
    LIMIT {$info['porPagina']} OFFSET {$info['offset']}
");
$stmt->execute($params);
$productos = $stmt->fetchAll();

$categorias = db()->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
$marcas = db()->query('SELECT * FROM marcas ORDER BY nombre')->fetchAll();
$tallas = array_column(db()->query("SELECT DISTINCT talla FROM productos WHERE talla IS NOT NULL AND talla != '' ORDER BY talla")->fetchAll(), 'talla');

require __DIR__ . '/includes/header.php';

function filtrosPanel(array $categorias, array $marcas, array $tallas, array $categoriasSel, array $marcasSel, array $tallasSel, string $precioMin, string $precioMax, string $disponibilidadSel): void
{
?>
<form method="get" action="catalogo.php" class="form-catalogo">
    <?php if (!empty($_GET['q'])): ?><input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>"><?php endif; ?>
    <?php if (!empty($_GET['orden'])): ?><input type="hidden" name="orden" value="<?= htmlspecialchars($_GET['orden']) ?>"><?php endif; ?>
    <div class="filtro-grupo">
        <h3>Disponibilidad</h3>
        <?php foreach (['disponible' => 'Disponible', 'agotado' => 'Agotado', '' => 'Todos'] as $valor => $texto): ?>
            <label><input type="radio" name="disponibilidad" value="<?= $valor ?>" <?= $disponibilidadSel === $valor ? 'checked' : '' ?>> <?= $texto ?></label>
        <?php endforeach; ?>
    </div>
    <div class="filtro-grupo">
        <h3>Categoría</h3>
        <?php foreach ($categorias as $c): ?>
            <label><input type="checkbox" name="categoria[]" value="<?= $c['id'] ?>" <?= in_array($c['id'], $categoriasSel) ? 'checked' : '' ?>> <?= htmlspecialchars($c['nombre']) ?></label>
        <?php endforeach; ?>
    </div>
    <div class="filtro-grupo">
        <h3>Marca</h3>
        <?php foreach ($marcas as $m): ?>
            <label><input type="checkbox" name="marca[]" value="<?= $m['id'] ?>" <?= in_array($m['id'], $marcasSel) ? 'checked' : '' ?>> <?= htmlspecialchars($m['nombre']) ?></label>
        <?php endforeach; ?>
    </div>
    <?php if ($tallas): ?>
    <div class="filtro-grupo">
        <h3>Talla</h3>
        <?php foreach ($tallas as $t): ?>
            <label><input type="checkbox" name="talla[]" value="<?= htmlspecialchars($t) ?>" <?= in_array($t, $tallasSel) ? 'checked' : '' ?>> <?= htmlspecialchars($t) ?></label>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="filtro-grupo">
        <h3>Precio</h3>
        <div style="display:flex;gap:8px;">
            <input type="number" name="precio_min" placeholder="Mín" value="<?= htmlspecialchars($precioMin) ?>" style="width:50%;padding:8px;border:1px solid var(--borde);">
            <input type="number" name="precio_max" placeholder="Máx" value="<?= htmlspecialchars($precioMax) ?>" style="width:50%;padding:8px;border:1px solid var(--borde);">
        </div>
    </div>
    <noscript><button type="submit" class="btn btn-block" style="margin-top:16px;">Aplicar filtros</button></noscript>
    <a href="catalogo.php" class="btn btn-outline btn-block js-limpiar-filtros" style="margin-top:16px;color:var(--negro);">Limpiar filtros</a>
</form>
<?php
}
?>

<div class="contenedor" style="padding-top:32px;">
    <div class="seccion-titulo" style="text-align:left;margin-bottom:24px;">
        <h1 style="font-size:2rem;">Catálogo</h1>
        <p id="catalogo-total"><?= $info['total'] ?> prenda<?= $info['total'] === 1 ? '' : 's' ?></p>
    </div>

    <div x-data="{ filtrosMovilAbierto: false }">
        <button type="button" class="btn btn-outline filtros-toggle-movil" style="color:var(--negro);" @click="filtrosMovilAbierto = !filtrosMovilAbierto">Filtros y orden</button>
        <div class="filtros-movil-panel card" id="filtros-movil" :class="{ abierto: filtrosMovilAbierto }" style="border:1px solid var(--borde);border-radius:6px;padding:16px;margin-bottom:16px;">
            <?php filtrosPanel($categorias, $marcas, $tallas, $categoriasSel, $marcasSel, $tallasSel, (string) $precioMin, (string) $precioMax, $disponibilidadSel); ?>
        </div>
    </div>

    <div class="catalogo-layout">
        <aside class="filtros-desktop" id="filtros-desktop">
            <?php filtrosPanel($categorias, $marcas, $tallas, $categoriasSel, $marcasSel, $tallasSel, (string) $precioMin, (string) $precioMax, $disponibilidadSel); ?>
        </aside>
        <div id="catalogo-resultados" style="transition:opacity .15s;">
            <form method="get" action="catalogo.php" class="form-catalogo" style="text-align:right;margin-bottom:16px;">
                <?php
                // Conserva todos los filtros activos al cambiar el orden.
                $qsFiltros = $_GET;
                unset($qsFiltros['orden'], $qsFiltros['pagina']);
                foreach (explode('&', http_build_query($qsFiltros)) as $par):
                    if ($par === '') continue;
                    [$k, $v] = array_pad(explode('=', $par, 2), 2, '');
                    echo '<input type="hidden" name="' . htmlspecialchars(urldecode($k)) . '" value="' . htmlspecialchars(urldecode($v)) . '">';
                endforeach;
                ?>
                <label style="font-size:0.85rem;">Ordenar por
                    <select name="orden" style="padding:8px;border:1px solid var(--borde);margin-left:6px;">
                        <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
                        <option value="precio_asc" <?= $orden === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                    </select>
                </label>
            </form>

            <?php if ($productos): ?>
            <?php $qsBase = $_GET; unset($qsBase['pagina']); ?>
            <div class="grid-productos" id="grid-catalogo"
                 data-qs="<?= htmlspecialchars(http_build_query($qsBase)) ?>"
                 data-siguiente-pagina="2"
                 data-total-paginas="<?= $info['totalPaginas'] ?>">
                <?php foreach ($productos as $prod): ?>
                    <?php require __DIR__ . '/includes/producto_card.php'; ?>
                <?php endforeach; ?>
            </div>
            <div id="catalogo-cargando" style="text-align:center;padding:24px;color:var(--gris);font-size:0.85rem;display:none;">Cargando más prendas...</div>
            <div id="catalogo-sentinel" style="height:1px;"></div>
            <?php else: ?>
                <div class="aviso-vacio">No se encontraron prendas con esos filtros.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function() {
    // Scroll infinito: se reinicia cada vez que los filtros reemplazan la grilla.
    var observer = null;
    function iniciarScrollInfinito() {
        if (observer) observer.disconnect();
        var grid = document.getElementById('grid-catalogo');
        var sentinel = document.getElementById('catalogo-sentinel');
        var cargando = document.getElementById('catalogo-cargando');
        if (!grid || !sentinel) return;
        var enCurso = false;

        var obs = observer = new IntersectionObserver(function(entradas) {
            entradas.forEach(function(entrada) {
                if (entrada.isIntersecting) cargarSiguiente();
            });
        }, { rootMargin: '600px' });
        obs.observe(sentinel);

        function cargarSiguiente() {
            var pagina = parseInt(grid.dataset.siguientePagina, 10);
            var totalPaginas = parseInt(grid.dataset.totalPaginas, 10);
            if (enCurso || pagina > totalPaginas) {
                if (pagina > totalPaginas) obs.disconnect();
                return;
            }
            enCurso = true;
            cargando.style.display = 'block';
            fetch('catalogo_cargar.php?' + grid.dataset.qs + '&pagina=' + pagina)
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    grid.insertAdjacentHTML('beforeend', html);
                    grid.dataset.siguientePagina = pagina + 1;
                    cargando.style.display = 'none';
                    enCurso = false;
                    if (pagina + 1 > totalPaginas) obs.disconnect();
                })
                .catch(function() {
                    cargando.style.display = 'none';
                    enCurso = false;
                });
        }
    }

    // Filtros dinámicos: cada cambio recarga solo los resultados, sin botón "Aplicar".
    var resultados = document.getElementById('catalogo-resultados');
    var peticionActual = 0;

    function cargarCatalogo(qs, agregarHistorial) {
        var url = 'catalogo.php' + (qs ? '?' + qs : '');
        var idPeticion = ++peticionActual;
        resultados.style.opacity = '0.5';
        fetch(url)
            .then(function(r) { return r.text(); })
            .then(function(html) {
                if (idPeticion !== peticionActual) return; // llegó una respuesta más nueva
                var doc = new DOMParser().parseFromString(html, 'text/html');
                ['catalogo-total', 'catalogo-resultados', 'filtros-desktop'].forEach(function(id) {
                    var nuevo = doc.getElementById(id);
                    var actual = document.getElementById(id);
                    if (nuevo && actual) actual.innerHTML = nuevo.innerHTML;
                });
                // En el panel móvil solo se cambia el formulario, para que no se cierre.
                var movilNuevo = doc.querySelector('#filtros-movil form');
                var movilActual = document.querySelector('#filtros-movil form');
                if (movilNuevo && movilActual) movilActual.replaceWith(document.importNode(movilNuevo, true));
                resultados.style.opacity = '';
                if (agregarHistorial) history.pushState(null, '', url);
                iniciarScrollInfinito();
            })
            .catch(function() {
                window.location.href = url; // si algo falla, recarga la página normal
            });
    }

    function qsDeFormulario(form) {
        var params = new URLSearchParams();
        new FormData(form).forEach(function(valor, clave) {
            if (valor !== '') params.append(clave, valor);
        });
        return params.toString();
    }

    document.addEventListener('change', function(e) {
        var form = e.target.closest('form.form-catalogo');
        if (form) cargarCatalogo(qsDeFormulario(form), true);
    });
    document.addEventListener('submit', function(e) {
        var form = e.target.closest('form.form-catalogo');
        if (!form) return;
        e.preventDefault();
        cargarCatalogo(qsDeFormulario(form), true);
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.js-limpiar-filtros')) return;
        e.preventDefault();
        cargarCatalogo('', true);
    });
    window.addEventListener('popstate', function() {
        cargarCatalogo(window.location.search.replace(/^\?/, ''), false);
    });

    iniciarScrollInfinito();
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
