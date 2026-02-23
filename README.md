# demo-symfony
MiniInventory API es un demo backend en Symfony que simula un sistema de inventario/catálogo: expone una API REST para consultar y administrar productos. En la Parte 3 se agrega seguridad (JWT + roles) y en la Parte 2 se conecta a MongoDB.

---

## Cómo correr (PowerShell / Docker Desktop)


```bash
docker compose pull
docker compose up -d --build
docker compose ps
docker compose exec php composer install
docker compose exec php php bin/console debug:router
```

**Comando extra (si falla por permisos en `var/`):**

```bash
docker compose exec php chmod -R 777 var
```

## URLs útiles (para el demo):

* API: `http://localhost:8080`
* Health: `http://localhost:8080/api/health`
* Products (mock Parte 1): `http://localhost:8080/api/products`
* Mongo Express: `http://localhost:8081`

