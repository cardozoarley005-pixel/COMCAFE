package clases;

public class Usuario {
    private int idUsuario;
    private String nombre;
    private String apellido;

    public String getNombreCompleto() {
        return this.nombre + " " + this.apellido;
    }
}


public class Secretaria extends Usuario {
    private String codigoTurno;
    public String registrarCompra() {
        return "Compra registrada exitosamente en el sistema por: " + getNombreCompleto();
    }

    public String verificarCaja() {
        return "Caja del turno operativo balanceada correctamente.";
    }
}
import java.util.Date;

public class Documento {
    protected int numeroDocumento;
    protected Date fechaEmision;
}