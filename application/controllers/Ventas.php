<?php

class Ventas extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model("VentaModel");
        $this->load->library("session");
    }
    public function index(){
        $ventasRealizadas = $this->VentaModel->todas();
        $datos = array("ventas" => $ventasRealizadas);

        $this->load->view("encabezado");
        $this->load->view("ventas/todas", $datos);
        $this->load->view("pie");
    }

    public function detalle($id){
        $detallesDeVenta = $this->VentaModel->porId($id);
        # Por si no existe la venta...
        if($detallesDeVenta->detalles === null){
            $this->session->set_flashdata(array(
                "mensaje" => "Los detalles de la venta no se pueden ver porque no existe una venta con ese ID",
                "clase" => "warning",
            ));
            redirect("ventas/");
        }
        $datos = array("venta" => $detallesDeVenta);
        $this->load->view("encabezado");
        $this->load->view("ventas/detalle", $datos);
        $this->load->view("pie");
    }

    public function generarReporte($id){
        include 'plantilla.php';
        require 'conexion.php';
        $query = "SELECT id_producto, cantidad, precio FROM productos_vendidos WHERE id_venta".'='.$id.";";
	    $resultado = $mysqli->query($query);
	
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        $pdf->SetFillColor(232,232,232);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(60,6,'ID_PRODUCTO',1,0,'C',1);
        $pdf->Cell(60,6,'CANTIDAD',1,0,'C',1);
        $pdf->Cell(40,6,'COSTO',1,0,'C',1);
        $pdf->Cell(30,6,'TOTAL',1,1,'C',1);

        $pdf->SetFont('Arial','',10);
        
        while($row = $resultado->fetch_assoc())
        {
            $pdf->Cell(60,6,utf8_decode($row['id_producto']),1,0,'C');
            $pdf->Cell(60,6,$row['cantidad'],1,0,'C');
            $pdf->Cell(40,6,utf8_decode($row['precio']),1,0,'C');
            $pdf->Cell(30,6,utf8_decode($row['precio'] * $row['cantidad']),1,1,'C');
        }
        $pdf->Output();
    }

    public function eliminar($id){
        $resultado = $this->VentaModel->eliminar($id);
        if($resultado){
            $mensaje = "Venta eliminada";
            $clase = "success";
        }else{
            $mensaje = "Error al eliminar la venta";
            $clase = "warning";
        }
        $this->session->set_flashdata(array(
            "mensaje" => $mensaje,
            "clase" => $clase,
        ));
        redirect("ventas/");
    }
}
?>