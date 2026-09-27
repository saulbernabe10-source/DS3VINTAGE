<?php
require_once __DIR__ . '/db.php';

/**
 * Configuración editable desde el admin (WhatsApp, Yape/Plin, QR).
 * Si algo no se ha configurado ahí, cae al valor fijo de config.php.
 */
function configuracion(): array
{
    static $config = null;
    if ($config === null) {
        $config = db()->query('SELECT * FROM configuracion WHERE id = 1')->fetch() ?: [];
    }
    return $config;
}

function whatsappNumero(): string
{
    return configuracion()['whatsapp_number'] ?: WHATSAPP_NUMBER;
}

function yapePlinNumero(): string
{
    return configuracion()['yape_plin_number'] ?: YAPE_PLIN_NUMBER;
}

function yapeQrImg(): ?string
{
    $ruta = configuracion()['yape_qr_img'] ?: null;
    if ($ruta && is_file(__DIR__ . '/../' . $ruta)) {
        return $ruta;
    }
    return null;
}

/** Los 25 departamentos del Perú, para el selector de envío por Shalom. */
function departamentosPeru(): array
{
    return [
        'Amazonas', 'Áncash', 'Apurímac', 'Arequipa', 'Ayacucho', 'Cajamarca', 'Callao',
        'Cusco', 'Huancavelica', 'Huánuco', 'Ica', 'Junín', 'La Libertad', 'Lambayeque',
        'Lima', 'Loreto', 'Madre de Dios', 'Moquegua', 'Pasco', 'Piura', 'Puno',
        'San Martín', 'Tacna', 'Tumbes', 'Ucayali',
    ];
}

/** Provincias del Perú por departamento (fuente: INEI/UBIGEO), para el selector de envío por Shalom. */
function provinciasPeru(): array
{
    return [
        'Amazonas' => ['Chachapoyas', 'Bagua', 'Bongará', 'Condorcanqui', 'Luya', 'Rodríguez de Mendoza', 'Utcubamba'],
        'Áncash' => ['Huaraz', 'Aija', 'Antonio Raymondi', 'Asunción', 'Bolognesi', 'Carhuaz', 'Carlos Fermín Fitzcarrald', 'Casma', 'Corongo', 'Huari', 'Huarmey', 'Huaylas', 'Mariscal Luzuriaga', 'Ocros', 'Pallasca', 'Pomabamba', 'Recuay', 'Santa', 'Sihuas', 'Yungay'],
        'Apurímac' => ['Abancay', 'Andahuaylas', 'Antabamba', 'Aymaraes', 'Cotabambas', 'Chincheros', 'Grau'],
        'Arequipa' => ['Arequipa', 'Camaná', 'Caravelí', 'Castilla', 'Caylloma', 'Condesuyos', 'Islay', 'La Unión'],
        'Ayacucho' => ['Huamanga', 'Cangallo', 'Huanca Sancos', 'Huanta', 'La Mar', 'Lucanas', 'Parinacochas', 'Páucar del Sara Sara', 'Sucre', 'Víctor Fajardo', 'Vilcas Huamán'],
        'Cajamarca' => ['Cajamarca', 'Cajabamba', 'Celendín', 'Chota', 'Contumazá', 'Cutervo', 'Hualgayoc', 'Jaén', 'San Ignacio', 'San Marcos', 'San Miguel', 'San Pablo', 'Santa Cruz'],
        'Callao' => ['Prov. Const. del Callao'],
        'Cusco' => ['Cusco', 'Acomayo', 'Anta', 'Calca', 'Canas', 'Canchis', 'Chumbivilcas', 'Espinar', 'La Convención', 'Paruro', 'Paucartambo', 'Quispicanchi', 'Urubamba'],
        'Huancavelica' => ['Huancavelica', 'Acobamba', 'Angaraes', 'Castrovirreyna', 'Churcampa', 'Huaytará', 'Tayacaja'],
        'Huánuco' => ['Huánuco', 'Ambo', 'Dos de Mayo', 'Huacaybamba', 'Huamalíes', 'Leoncio Prado', 'Marañón', 'Pachitea', 'Puerto Inca', 'Lauricocha', 'Yarowilca'],
        'Ica' => ['Ica', 'Chincha', 'Nasca', 'Palpa', 'Pisco'],
        'Junín' => ['Huancayo', 'Concepción', 'Chanchamayo', 'Jauja', 'Junín', 'Satipo', 'Tarma', 'Yauli', 'Chupaca'],
        'La Libertad' => ['Trujillo', 'Ascope', 'Bolívar', 'Chepén', 'Julcán', 'Otuzco', 'Pacasmayo', 'Pataz', 'Sánchez Carrión', 'Santiago de Chuco', 'Gran Chimú', 'Virú'],
        'Lambayeque' => ['Chiclayo', 'Ferreñafe', 'Lambayeque'],
        'Lima' => ['Lima', 'Barranca', 'Cajatambo', 'Canta', 'Cañete', 'Huaral', 'Huarochirí', 'Huaura', 'Oyón', 'Yauyos'],
        'Loreto' => ['Maynas', 'Alto Amazonas', 'Loreto', 'Mariscal Ramón Castilla', 'Requena', 'Ucayali', 'Datem del Marañón', 'Putumayo'],
        'Madre de Dios' => ['Tambopata', 'Manu', 'Tahuamanu'],
        'Moquegua' => ['Mariscal Nieto', 'General Sánchez Cerro', 'Ilo'],
        'Pasco' => ['Pasco', 'Daniel Alcides Carrión', 'Oxapampa'],
        'Piura' => ['Piura', 'Ayabaca', 'Huancabamba', 'Morropón', 'Paita', 'Sullana', 'Talara', 'Sechura'],
        'Puno' => ['Puno', 'Azángaro', 'Carabaya', 'Chucuito', 'El Collao', 'Huancané', 'Lampa', 'Melgar', 'Moho', 'San Antonio de Putina', 'San Román', 'Sandia', 'Yunguyo'],
        'San Martín' => ['Moyobamba', 'Bellavista', 'El Dorado', 'Huallaga', 'Lamas', 'Mariscal Cáceres', 'Picota', 'Rioja', 'San Martín', 'Tocache'],
        'Tacna' => ['Tacna', 'Candarave', 'Jorge Basadre', 'Tarata'],
        'Tumbes' => ['Tumbes', 'Contralmirante Villar', 'Zarumilla'],
        'Ucayali' => ['Coronel Portillo', 'Atalaya', 'Padre Abad', 'Purús'],
    ];
}

function slugify(string $texto): string
{
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
    $texto = strtolower(trim($texto));
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    return trim($texto, '-');
}

function slugUnico(string $tabla, string $base, ?int $ignorarId = null): string
{
    $slug = slugify($base);
    $original = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$tabla} WHERE slug = ?" . ($ignorarId ? " AND id != ?" : "");
        $params = $ignorarId ? [$slug, $ignorarId] : [$slug];
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $original . '-' . $i;
        $i++;
    }
}

function formatPrecio(float $precio): string
{
    return DS3_CURRENCY_SYMBOL . ' ' . number_format($precio, 2);
}

/**
 * Calcula el precio final de un producto aplicando la campaña de descuento
 * activa y vigente (por fecha), si existe, sobre precio_oferta o precio base.
 * Se resuelve al vuelo para que las campañas se desactiven solas al vencer.
 */
function precioFinal(array $producto): array
{
    $base = $producto['precio_oferta'] !== null && $producto['precio_oferta'] > 0
        ? (float) $producto['precio_oferta']
        : (float) $producto['precio'];

    $stmt = db()->prepare("
        SELECT c.porcentaje
        FROM campanas_descuento c
        LEFT JOIN campana_productos cp ON cp.campana_id = c.id AND cp.producto_id = ?
        LEFT JOIN campana_categorias cc ON cc.campana_id = c.id AND cc.categoria_id = ?
        WHERE c.activo = 1
          AND NOW() BETWEEN c.fecha_inicio AND c.fecha_fin
          AND (cp.producto_id IS NOT NULL OR cc.categoria_id IS NOT NULL)
        ORDER BY c.porcentaje DESC
        LIMIT 1
    ");
    $stmt->execute([$producto['id'], $producto['categoria_id']]);
    $campana = $stmt->fetch();

    if ($campana) {
        $final = round($base * (1 - ((float) $campana['porcentaje'] / 100)), 2);
        return ['precio' => $final, 'precio_anterior' => $base, 'descuento_pct' => (float) $campana['porcentaje']];
    }

    $precioBaseOriginal = (float) $producto['precio'];
    if ($base < $precioBaseOriginal) {
        return ['precio' => $base, 'precio_anterior' => $precioBaseOriginal, 'descuento_pct' => null];
    }

    return ['precio' => $base, 'precio_anterior' => null, 'descuento_pct' => null];
}

function actualizarEstadoStock(int $productoId): void
{
    $stmt = db()->prepare("UPDATE productos SET estado = IF(stock <= 0, 'agotado', 'activo') WHERE id = ?");
    $stmt->execute([$productoId]);
}

/**
 * Sube y redimensiona una imagen de producto o comprobante de pago.
 * Devuelve la ruta relativa guardada, o null si no se subió nada.
 */
function subirImagen(array $archivo, string $destinoDir, string $destinoUrl, int $anchoMax = 1600): ?string
{
    if (!isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Error al subir el archivo.');
    }

    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($archivo['tmp_name']);

    // Fotos de iPhone (HEIC/HEIF): se convierten a JPEG automáticamente si el
    // servidor tiene Imagick con soporte HEIF instalado.
    if (!isset($permitidos[$mime]) && in_array($mime, ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence'], true)) {
        if (extension_loaded('imagick') && count(Imagick::queryFormats('HEIC')) > 0) {
            try {
                $imagick = new Imagick($archivo['tmp_name']);
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality(85);
                $tmpJpeg = $archivo['tmp_name'] . '.jpg';
                $imagick->writeImage($tmpJpeg);
                $imagick->clear();
                $archivo['tmp_name'] = $tmpJpeg;
                $mime = 'image/jpeg';
            } catch (Throwable $e) {
                throw new RuntimeException('No se pudo convertir esta foto HEIC. Expórtala como JPG e inténtalo de nuevo.');
            }
        } else {
            throw new RuntimeException('Formato HEIC no soportado en este servidor. En tu iPhone: Ajustes > Cámara > Formatos > "Más compatible" (así tus fotos se guardan en JPG), o conviértela a JPG antes de subirla.');
        }
    }

    if (!isset($permitidos[$mime])) {
        throw new RuntimeException('Formato de imagen no permitido. Usa JPG, PNG o WEBP.');
    }

    if (!is_dir($destinoDir)) {
        mkdir($destinoDir, 0755, true);
    }

    $ext = $permitidos[$mime];
    $nombre = bin2hex(random_bytes(8)) . '.' . $ext;
    $rutaDestino = $destinoDir . $nombre;

    [$anchoOrig, $altoOrig] = getimagesize($archivo['tmp_name']);
    $origen = match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($archivo['tmp_name']),
        'image/png' => imagecreatefrompng($archivo['tmp_name']),
        'image/webp' => imagecreatefromwebp($archivo['tmp_name']),
    };

    if ($anchoOrig > $anchoMax) {
        $altoNuevo = (int) round($altoOrig * ($anchoMax / $anchoOrig));
        $redimensionada = imagecreatetruecolor($anchoMax, $altoNuevo);
        if ($mime === 'image/png') {
            imagealphablending($redimensionada, false);
            imagesavealpha($redimensionada, true);
        }
        imagecopyresampled($redimensionada, $origen, 0, 0, 0, 0, $anchoMax, $altoNuevo, $anchoOrig, $altoOrig);
        imagedestroy($origen);
        $origen = $redimensionada;
    }

    match ($mime) {
        'image/jpeg' => imagejpeg($origen, $rutaDestino, 85),
        'image/png' => imagepng($origen, $rutaDestino, 6),
        'image/webp' => imagewebp($origen, $rutaDestino, 85),
    };
    imagedestroy($origen);

    if (isset($tmpJpeg) && is_file($tmpJpeg)) {
        unlink($tmpJpeg);
    }

    return $destinoUrl . $nombre;
}

function paginar(string $tablaConJoins, string $where, array $params, int $pagina, int $porPagina = ADMIN_PAGE_SIZE): array
{
    $pagina = max(1, $pagina);
    $offset = ($pagina - 1) * $porPagina;

    $stmtTotal = db()->prepare("SELECT COUNT(*) AS total FROM {$tablaConJoins} WHERE {$where}");
    $stmtTotal->execute($params);
    $total = (int) $stmtTotal->fetch()['total'];

    return [
        'offset' => $offset,
        'porPagina' => $porPagina,
        'pagina' => $pagina,
        'totalPaginas' => max(1, (int) ceil($total / $porPagina)),
        'total' => $total,
    ];
}

/**
 * Arma el WHERE/params/orden del catálogo a partir de los filtros en $_GET.
 * Compartido entre catalogo.php (primera carga) y catalogo_cargar.php (scroll infinito)
 * para que ambos filtren exactamente igual.
 */
function filtrosCatalogoDesdeQuery(): array
{
    $q = trim($_GET['q'] ?? '');
    $categoriasSel = array_map('intval', (array) ($_GET['categoria'] ?? []));
    $marcasSel = array_map('intval', (array) ($_GET['marca'] ?? []));
    $tallasSel = (array) ($_GET['talla'] ?? []);
    $precioMin = $_GET['precio_min'] ?? '';
    $precioMax = $_GET['precio_max'] ?? '';
    $orden = $_GET['orden'] ?? 'recientes';

    $where = ["p.estado != ''"];
    $params = [];

    if ($q !== '') {
        $where[] = 'p.nombre LIKE ?';
        $params[] = '%' . $q . '%';
    }
    if ($categoriasSel) {
        $where[] = 'p.categoria_id IN (' . implode(',', array_fill(0, count($categoriasSel), '?')) . ')';
        $params = array_merge($params, $categoriasSel);
    }
    if ($marcasSel) {
        $where[] = 'p.marca_id IN (' . implode(',', array_fill(0, count($marcasSel), '?')) . ')';
        $params = array_merge($params, $marcasSel);
    }
    if ($tallasSel) {
        $where[] = 'p.talla IN (' . implode(',', array_fill(0, count($tallasSel), '?')) . ')';
        $params = array_merge($params, $tallasSel);
    }
    if ($precioMin !== '') {
        $where[] = 'COALESCE(p.precio_oferta, p.precio) >= ?';
        $params[] = (float) $precioMin;
    }
    if ($precioMax !== '') {
        $where[] = 'COALESCE(p.precio_oferta, p.precio) <= ?';
        $params[] = (float) $precioMax;
    }

    $ordenSql = match ($orden) {
        'precio_asc' => 'COALESCE(p.precio_oferta, p.precio) ASC',
        'precio_desc' => 'COALESCE(p.precio_oferta, p.precio) DESC',
        default => 'p.fecha_creacion DESC',
    };

    return [
        'where' => implode(' AND ', $where),
        'params' => $params,
        'orden' => $ordenSql,
        'q' => $q, 'categoriasSel' => $categoriasSel, 'marcasSel' => $marcasSel,
        'tallasSel' => $tallasSel, 'precioMin' => $precioMin, 'precioMax' => $precioMax,
    ];
}

/** Correo remitente fijo para avisos internos al staff (distinto del remitente de cara al cliente). */
const AVISO_ADMIN_REMITENTE = 'soporte@ds3vintage.com';

/**
 * A quién avisar cuando entra un pedido nuevo. Si no hay nadie configurado en
 * Configuración → Avisos, cae de vuelta a NOTIFY_EMAIL para no dejar de avisar.
 */
function destinatariosAvisos(): array
{
    $emails = db()->query('SELECT email FROM avisos_admin ORDER BY creado_en')->fetchAll(PDO::FETCH_COLUMN);
    return $emails ?: [NOTIFY_EMAIL];
}

function enviarNotificacionPedido(int $pedidoId): void
{
    $stmt = db()->prepare("SELECT * FROM pedidos WHERE id = ?");
    $stmt->execute([$pedidoId]);
    $pedido = $stmt->fetch();
    if (!$pedido) {
        return;
    }

    $asunto = "Nuevo pedido #{$pedido['id']} pendiente de aprobación — " . SITE_NAME;
    $cuerpo = "Cliente: {$pedido['cliente_nombre']}\n"
        . "Celular: {$pedido['cliente_celular']}\n"
        . "Método de pago: {$pedido['metodo_pago']}\n"
        . "Total: " . formatPrecio((float) $pedido['total']) . "\n"
        . "Estado: {$pedido['estado']}\n"
        . "\nRevisar y aprobar: " . SITE_URL . "/admin/pedido_detalle.php?id={$pedido['id']}\n";

    $cabeceras = "From: " . SITE_NAME . " <" . AVISO_ADMIN_REMITENTE . ">\r\n";
    // mail() depende de que el hosting/cPanel tenga sendmail configurado.
    foreach (destinatariosAvisos() as $destinatario) {
        @mail($destinatario, $asunto, $cuerpo, $cabeceras);
    }
}

/**
 * Construye el HTML del recibo/comprobante de conformidad de un pedido.
 * Se usa tanto para el correo al cliente como para la vista imprimible del admin.
 */
function reciboHtml(array $pedido, array $items): string
{
    $filas = '';
    foreach ($items as $it) {
        $filas .= '<tr>'
            . '<td style="padding:8px;border-bottom:1px solid #e2dcd0;">' . htmlspecialchars($it['producto_nombre']) . ($it['talla'] ? ' (Talla ' . htmlspecialchars($it['talla']) . ')' : '') . '</td>'
            . '<td style="padding:8px;border-bottom:1px solid #e2dcd0;text-align:center;">' . (int) $it['cantidad'] . '</td>'
            . '<td style="padding:8px;border-bottom:1px solid #e2dcd0;text-align:right;">' . formatPrecio((float) $it['precio_unitario']) . '</td>'
            . '<td style="padding:8px;border-bottom:1px solid #e2dcd0;text-align:right;">' . formatPrecio((float) $it['precio_unitario'] * $it['cantidad']) . '</td>'
            . '</tr>';
    }

    $entrega = $pedido['tipo_entrega'] === 'shalom'
        ? 'Envío por Shalom a ' . htmlspecialchars($pedido['shalom_ciudad'] ?? '') . ', ' . htmlspecialchars($pedido['shalom_departamento'] ?? '') . ' (coordinamos la agencia exacta por WhatsApp)'
        : 'Recojo en Tacna (coordinamos por WhatsApp)';

    $metodos = ['whatsapp' => 'WhatsApp', 'yape_plin' => 'Yape / Plin', 'transferencia' => 'Transferencia interbancaria'];

    return '
    <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#171412;">
        <h2 style="font-family:\'Arial Black\',Arial,sans-serif;">' . SITE_NAME . '</h2>
        <p style="color:#6f6a5e;">Recibo / conformidad de pago — Pedido #' . (int) $pedido['id'] . '</p>
        <p>Fecha: ' . htmlspecialchars(date('d/m/Y H:i', strtotime($pedido['fecha_creacion']))) . '</p>
        <hr style="border:none;border-top:1px solid #e2dcd0;">
        <p><strong>Cliente:</strong> ' . htmlspecialchars($pedido['cliente_nombre']) . '<br>
        <strong>DNI:</strong> ' . htmlspecialchars($pedido['cliente_dni']) . '<br>
        <strong>Celular:</strong> ' . htmlspecialchars($pedido['cliente_celular']) . '<br>
        <strong>Email:</strong> ' . htmlspecialchars($pedido['cliente_email']) . '</p>
        <p><strong>Entrega:</strong> ' . $entrega . '</p>
        <p><strong>Método de pago:</strong> ' . ($metodos[$pedido['metodo_pago']] ?? htmlspecialchars($pedido['metodo_pago'])) . '</p>
        <table style="width:100%;border-collapse:collapse;margin-top:12px;">
            <thead><tr style="background:#efeae1;">
                <th style="padding:8px;text-align:left;">Prenda</th>
                <th style="padding:8px;">Cant.</th>
                <th style="padding:8px;text-align:right;">Precio</th>
                <th style="padding:8px;text-align:right;">Subtotal</th>
            </tr></thead>
            <tbody>' . $filas . '</tbody>
        </table>
        <p style="text-align:right;font-size:1.2rem;margin-top:10px;"><strong>Total: ' . formatPrecio((float) $pedido['total']) . '</strong></p>
        <hr style="border:none;border-top:1px solid #e2dcd0;">
        <p style="color:#6f6a5e;font-size:0.85rem;">Gracias por tu compra en ' . SITE_NAME . '. Pago confirmado — tu pedido pasa a preparación.</p>
    </div>';
}

function enviarRecibo(int $pedidoId): bool
{
    $stmt = db()->prepare('SELECT * FROM pedidos WHERE id = ?');
    $stmt->execute([$pedidoId]);
    $pedido = $stmt->fetch();
    if (!$pedido || !$pedido['cliente_email']) {
        return false;
    }

    $stmtItems = db()->prepare('SELECT * FROM pedido_items WHERE pedido_id = ?');
    $stmtItems->execute([$pedidoId]);
    $items = $stmtItems->fetchAll();

    $asunto = 'Recibo de tu compra #' . $pedido['id'] . ' — ' . SITE_NAME;
    $cuerpo = reciboHtml($pedido, $items);
    $cabeceras = "MIME-Version: 1.0\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n"
        . "From: " . SITE_NAME . " <" . NOTIFY_EMAIL . ">\r\n";

    $enviado = @mail($pedido['cliente_email'], $asunto, $cuerpo, $cabeceras);
    if ($enviado) {
        $upd = db()->prepare('UPDATE pedidos SET recibo_enviado = 1 WHERE id = ?');
        $upd->execute([$pedidoId]);
    }
    return $enviado;
}

/**
 * Avisa al cliente por correo cada vez que cambia el estado de su pedido, para
 * que sepa en qué va sin tener que preguntar. "pagado" usa el recibo completo
 * (enviarRecibo); el resto de estados manda un aviso corto.
 */
function notificarCambioEstado(int $pedidoId, string $estadoNuevo): bool
{
    if ($estadoNuevo === 'pagado') {
        return enviarRecibo($pedidoId);
    }

    $stmt = db()->prepare('SELECT * FROM pedidos WHERE id = ?');
    $stmt->execute([$pedidoId]);
    $pedido = $stmt->fetch();
    if (!$pedido || !$pedido['cliente_email']) {
        return false;
    }

    if ($estadoNuevo === 'cancelado') {
        return enviarCancelacion($pedidoId);
    }

    $mensajes = [
        'pendiente_pago' => 'Tu pedido está pendiente de pago.',
        'en_preparacion' => '¡Tu pago fue validado y tu pedido ya está en preparación!',
        'entregado' => 'Tu pedido fue entregado. ¡Gracias por comprar en ' . SITE_NAME . '!',
    ];
    $mensaje = $mensajes[$estadoNuevo] ?? ('Tu pedido cambió de estado a: ' . str_replace('_', ' ', $estadoNuevo));

    $asunto = 'Actualización de tu pedido #' . $pedido['id'] . ' — ' . SITE_NAME;
    $cuerpo = '
    <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#171412;">
        <h2 style="font-family:\'Arial Black\',Arial,sans-serif;">' . SITE_NAME . '</h2>
        <p>Hola ' . htmlspecialchars($pedido['cliente_nombre']) . ',</p>
        <p style="font-size:1.05rem;">' . $mensaje . '</p>
        <p style="color:#6f6a5e;font-size:0.9rem;">Pedido #' . (int) $pedido['id'] . ' — Total ' . formatPrecio((float) $pedido['total']) . '</p>
        <p style="color:#6f6a5e;font-size:0.85rem;">¿Dudas? Escríbenos por WhatsApp al ' . htmlspecialchars(whatsappNumero()) . '.</p>
    </div>';
    $cabeceras = "MIME-Version: 1.0\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n"
        . "From: " . SITE_NAME . " <" . NOTIFY_EMAIL . ">\r\n";

    return @mail($pedido['cliente_email'], $asunto, $cuerpo, $cabeceras);
}

/**
 * Correo de cancelación: más detallado que un simple aviso — explica que hubo
 * un problema, incluye el detalle del pedido como referencia, e invita al
 * cliente a escribir por WhatsApp con su recibo para resolver (devolución u
 * otra respuesta al inconveniente).
 */
function enviarCancelacion(int $pedidoId): bool
{
    $stmt = db()->prepare('SELECT * FROM pedidos WHERE id = ?');
    $stmt->execute([$pedidoId]);
    $pedido = $stmt->fetch();
    if (!$pedido || !$pedido['cliente_email']) {
        return false;
    }

    $stmtItems = db()->prepare('SELECT * FROM pedido_items WHERE pedido_id = ?');
    $stmtItems->execute([$pedidoId]);
    $items = $stmtItems->fetchAll();

    $filas = '';
    foreach ($items as $it) {
        $filas .= '<tr>'
            . '<td style="padding:6px 8px;border-bottom:1px solid #e2dcd0;">' . htmlspecialchars($it['producto_nombre']) . ($it['talla'] ? ' (Talla ' . htmlspecialchars($it['talla']) . ')' : '') . '</td>'
            . '<td style="padding:6px 8px;border-bottom:1px solid #e2dcd0;text-align:right;">' . formatPrecio((float) $it['precio_unitario'] * $it['cantidad']) . '</td>'
            . '</tr>';
    }

    $mensajeWa = 'Hola, tengo una consulta sobre mi pedido #' . $pedido['id'] . ' que fue cancelado.';
    $linkWa = 'https://wa.me/' . whatsappNumero() . '?text=' . urlencode($mensajeWa);

    $asunto = 'Tuvimos un problema con tu pedido #' . $pedido['id'] . ' — ' . SITE_NAME;
    $cuerpo = '
    <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#171412;">
        <h2 style="font-family:\'Arial Black\',Arial,sans-serif;">' . SITE_NAME . '</h2>
        <p>Hola ' . htmlspecialchars($pedido['cliente_nombre']) . ',</p>
        <p style="font-size:1.05rem;">Te escribimos porque tuvimos un inconveniente con tu pedido <strong>#' . (int) $pedido['id'] . '</strong> y, lamentablemente, tuvimos que cancelarlo.</p>
        <p>Sabemos que esto puede ser una molestia, y queremos resolverlo contigo lo antes posible — ya sea aclarar qué pasó, coordinar una devolución si corresponde, o buscar una alternativa.</p>
        <table style="width:100%;border-collapse:collapse;margin-top:12px;">
            <thead><tr style="background:#efeae1;">
                <th style="padding:6px 8px;text-align:left;">Prenda</th>
                <th style="padding:6px 8px;text-align:right;">Subtotal</th>
            </tr></thead>
            <tbody>' . $filas . '</tbody>
        </table>
        <p style="text-align:right;margin-top:6px;"><strong>Total: ' . formatPrecio((float) $pedido['total']) . '</strong></p>
        <p style="margin-top:20px;">Por favor escríbenos por WhatsApp adjuntando (o mencionando) tu recibo de compra, y con gusto te ayudamos a resolverlo:</p>
        <p style="text-align:center;margin:16px 0;">
            <a href="' . htmlspecialchars($linkWa) . '" style="display:inline-block;background:#25D366;color:#fff;padding:10px 22px;border-radius:4px;text-decoration:none;font-weight:600;">Escribirnos por WhatsApp</a>
        </p>
        <p style="color:#6f6a5e;font-size:0.85rem;">Pedido #' . (int) $pedido['id'] . ' — ' . htmlspecialchars(date('d/m/Y H:i', strtotime($pedido['fecha_creacion']))) . '</p>
        <hr style="border:none;border-top:1px solid #e2dcd0;">
        <p style="color:#6f6a5e;font-size:0.85rem;">Lamentamos el inconveniente y gracias por tu paciencia.</p>
    </div>';
    $cabeceras = "MIME-Version: 1.0\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n"
        . "From: " . SITE_NAME . " <" . NOTIFY_EMAIL . ">\r\n";

    return @mail($pedido['cliente_email'], $asunto, $cuerpo, $cabeceras);
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfVerificar(?string $token): bool
{
    return $token !== null && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}
