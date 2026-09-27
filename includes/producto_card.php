<?php
/** Espera $prod (fila de productos con columna 'foto') y $rutaBase definidos antes de incluir. */
$precios = precioFinal($prod);
?>
<a class="tarjeta-producto" href="<?= $rutaBase ?? '' ?>producto.php?slug=<?= urlencode($prod['slug']) ?>">
    <div class="foto-wrap">
        <?php if ($prod['estado'] === 'agotado'): ?>
            <span class="etiqueta etiqueta-agotado">Agotado</span>
        <?php elseif ($precios['precio_anterior']): ?>
            <span class="etiqueta">Oferta</span>
        <?php endif; ?>
        <?php if ($precios['precio_anterior']): ?>
            <?php $pct = $precios['descuento_pct'] ?? round((1 - $precios['precio'] / $precios['precio_anterior']) * 100); ?>
            <span class="etiqueta-descuento">-<?= (int) $pct ?>%</span>
        <?php endif; ?>
        <?php if (!empty($prod['foto'])): ?>
            <img src="<?= $rutaBase ?? '' ?><?= htmlspecialchars($prod['foto']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>" loading="lazy">
        <?php endif; ?>
    </div>
    <?php if (!empty($prod['marca_nombre'])): ?><div class="marca"><?= htmlspecialchars($prod['marca_nombre']) ?></div><?php endif; ?>
    <div class="nombre"><?= htmlspecialchars($prod['nombre']) ?></div>
    <div class="precios">
        <span class="actual"><?= formatPrecio($precios['precio']) ?></span>
        <?php if ($precios['precio_anterior']): ?><span class="anterior"><?= formatPrecio($precios['precio_anterior']) ?></span><?php endif; ?>
    </div>
</a>
