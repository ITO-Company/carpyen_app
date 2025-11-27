import { usePage } from '@inertiajs/vue3'

export function usePermisos() {
  const page = usePage()
  const user = page.props.auth.user

  // Obtener rol del usuario
  const rol = user?.rol || null

  // Verificar si el usuario tiene un rol específico
  const tiene = (roles) => {
    if (!user) return false
    if (typeof roles === 'string') {
      return rol === roles
    }
    return roles.includes(rol)
  }

  // Permisos específicos por módulo
  const permisos = {
    // Clientes
    cliente: {
      ver: tiene(['ADMIN', 'VENDEDOR']),
      crear: tiene(['ADMIN', 'VENDEDOR']),
      editar: tiene('ADMIN'),
      eliminar: tiene('ADMIN'),
    },

    // Proyectos
    proyecto: {
      ver: tiene(['ADMIN', 'VENDEDOR']),
      verLista: tiene(['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR', 'INSTALADOR']),
      crear: tiene(['ADMIN', 'VENDEDOR']),
      editar: (proyecto) => {
        if (tiene('ADMIN')) return true
        if (tiene('VENDEDOR') && proyecto?.user_id === user.id) return true
        return false
      },
      eliminar: tiene('ADMIN'),
    },

    // Productos
    producto: {
      ver: tiene(['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR']),
      crear: tiene(['ADMIN', 'JEFE_INSTALADOR']),
      editar: tiene(['ADMIN', 'JEFE_INSTALADOR']),
      eliminar: tiene('ADMIN'),
    },

    // Cronogramas
    cronograma: {
      ver: tiene(['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR']),
      crear: tiene(['ADMIN', 'VENDEDOR']),
      editar: (cronograma) => {
        if (tiene('ADMIN')) return true
        if (tiene('VENDEDOR')) return false // Los vendedores no pueden editar
        if (tiene('JEFE_INSTALADOR') && cronograma?.usuario_id === user.id) return true
        return false
      },
      eliminar: tiene('ADMIN'),
    },

    // Tareas
    tarea: {
      ver: (tarea) => {
        if (tiene('ADMIN')) return true
        if (tiene('JEFE_INSTALADOR') && tarea?.cronograma?.usuario_id === user.id) return true
        if (tiene('INSTALADOR')) return true // Los instaladores pueden ver
        return false
      },
      crear: (cronograma) => {
        if (tiene('ADMIN')) return true
        if (tiene('JEFE_INSTALADOR') && cronograma?.usuario_id === user.id) return true
        return false
      },
      editar: (tarea) => {
        if (tiene('ADMIN')) return true
        if (tiene('JEFE_INSTALADOR') && tarea?.cronograma?.usuario_id === user.id) return true
        if (tiene('INSTALADOR') && tarea?.user_id === user.id) return true
        return false
      },
      eliminar: (tarea) => {
        if (tiene('ADMIN')) return true
        if (tiene('JEFE_INSTALADOR') && tarea?.cronograma?.usuario_id === user.id) return true
        return false
      },
    },

    // Diseños
    diseno: {
      ver: true, // Todos pueden ver
      crear: true, // Todos pueden crear
      editar: tiene(['ADMIN', 'DISEÑADOR']), // Vendedores NO pueden editar
      eliminar: tiene('ADMIN'),
    },

    // Cotizaciones
    cotizacion: {
      ver: (cotizacion) => {
        if (tiene('ADMIN')) return true
        if (tiene('VENDEDOR') && cotizacion?.proyecto?.user_id === user.id) return true
        // JEFE_INSTALADOR no puede ver cotizaciones
        return false
      },
      crear: (proyecto) => {
        if (tiene('ADMIN')) return true
        if (tiene('VENDEDOR') && proyecto?.user_id === user.id) return true
        return false
      },
      editar: (cotizacion) => {
        if (tiene('ADMIN')) return true
        if (tiene('VENDEDOR') && cotizacion?.proyecto?.user_id === user.id) return true
        return false
      },
      eliminar: tiene('ADMIN'),
    },

    // ProyectoProductos
    proyectoProducto: {
      ver: tiene(['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR']),
      crear: tiene(['ADMIN', 'JEFE_INSTALADOR']),
      editar: tiene(['ADMIN', 'JEFE_INSTALADOR']),
      eliminar: (proyecto) => {
        if (tiene('ADMIN')) return true
        if (tiene('JEFE_INSTALADOR')) return true
        if (tiene('INSTALADOR')) return false
        return false
      },
    },
  }

  return {
    rol,
    user,
    tiene,
    permisos,
  }
}
