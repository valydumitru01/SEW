"use strict"
class MazeGame {
    constructor() {
        this.shapes = ["Ball", "Square", "Triangle"]
        let RED = "#f00"
        let GREEN = "#0f0"
        let BLUE = "#00f"

        this.RADIUS = 5
        this.MAPRES = 20
        this.menuCircleRadius = 100
        this.gameFinnished=false
        this.colors = [RED, GREEN, BLUE]
        this.mapString= "" +
            "00000000000000000000" +
            "000               00" +
            "000 0000000000000 00" +
            "000 000000FFF0000 00" +
            "000 000000FFF0000 00" +
            "000          0000 00" +
            "00000000000000000 00" +
            "00000000000000000 00" +
            "0000              00" +
            "0000 000000000000000" +
            "000   00000000000000" +
            "000 P 00000000000000"
        this.canvas = document.querySelector('canvas');
        this.ctx = this.canvas.getContext('2d');


        this.spaceAvailable = []
        this.collisionCreated = false
        this.canvasDrawLoop();
        this.canvasMenu()
        this.canvas.requestPointerLock = this.canvas.requestPointerLock || this.canvas.mozRequestPointerLock;

        document.exitPointerLock = document.exitPointerLock || document.mozExitPointerLock;

        var self = this
        this.canvas.onclick = function () {
            self.canvas.requestPointerLock();
        };
        this.updatePos = this.updatePosition.bind(this)

        document.addEventListener('pointerlockchange', this.lockChangeAlert.bind(this), false);
        document.addEventListener('mozpointerlockchange', this.lockChangeAlert.bind(this), false);

        this.animation;
        this.playerCreated = false
    }


    degToRad(degrees) {
        var result = Math.PI / 180 * degrees;
        return result;
    }
    emToPx(em) {
        return em * 16
    }
    dibujarIndicadorPosJugador(){
        this.ctx.beginPath();
        this.ctx.strokeStyle = '#28f';
        this.ctx.lineWidth = 40;
        this.ctx.arc(this.x, this.y, this.menuCircleRadius, 0, this.degToRad(360), true);
        this.ctx.stroke()
    }

    canvasMenu() {
        this.canvasMap()

        this.dibujarIndicadorPosJugador()

        this.ctx.fillStyle = "#30f";
        this.ctx.font = 'bold 3.5em Courier New';
        console.log(this.gameFinnished)
        if(!this.gameFinnished)
            this.ctx.fillText('Click para jugar', this.canvas.width / 2 - 280, this.canvas.height / 2);
        else{
            this.ctx.fillText('Has ganado!', this.canvas.width / 2 - 200, this.canvas.height / 2);
            console.log("HAS GANADO")
        }
        console.log(this.spaceAvailable)
    }

    canvasDrawLoop() {
        this.ctx.fillStyle = "rgb(248, 224, 210)";
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.canvasMap()
        this.setPlayer(this.createCircle.bind(this), this.x, this.y)

        if(this.gameFinnished){
            document.exitPointerLock()
            document.removeEventListener("mousemove", this.updatePos, false)
            this.gameFinnished=false
            this.canvasMap()
            this.playerCreated=false
        }
    }
    setPlayer(shape, x, y) {
        this.ctx.fillStyle = "#f00";
        shape(x, y)
    }
    checkFinish(blockX, blockY, blockWidth) {
        var self = this
        if (!(((this.y + self.RADIUS) < blockY) ||
            (this.y > (blockY + blockWidth)) ||
            ((this.x + self.RADIUS) < blockX) ||
            (this.x > (blockX + blockWidth)))) {
            this.gameFinnished=true
            console.log("ENTRASTE EN FINISH")
        }
    }
    checkCollisions(blockX, blockY, blockWidth) {
        var self = this
        if (!(((this.y + self.RADIUS) < blockY) ||
            (this.y > (blockY + blockWidth)) ||
            ((this.x + self.RADIUS) < blockX) ||
            (this.x > (blockX + blockWidth)))) {
            this.playerCreated = false
        }
    }
    canvasBlock(x, y) {
        this.ctx.fillStyle = "#f53"
        this.ctx.fillRect(x * this.MAPRES * 1.5, y * this.MAPRES * 1.5, this.canvas.width / this.MAPRES, this.canvas.width / this.MAPRES);
        this.ctx.lineJoin = 'bevel';
        this.ctx.lineWidth = 2;
        this.ctx.strokeStyle = '#f00';
        this.ctx.strokeRect(x * this.MAPRES * 1.5, y * this.MAPRES * 1.5, this.canvas.width / this.MAPRES - 2, this.canvas.width / this.MAPRES - 2)
        this.checkCollisions(x * this.MAPRES * 1.5, y * this.MAPRES * 1.5, this.canvas.width / this.MAPRES)
    }
    canvasFinish(x, y){
        this.ctx.fillStyle = "#df3"
        this.ctx.fillRect(x * this.MAPRES * 1.5, y * this.MAPRES * 1.5, this.canvas.width / this.MAPRES, this.canvas.width / this.MAPRES);
        this.checkFinish(x * this.MAPRES * 1.5, y * this.MAPRES * 1.5, this.canvas.width / this.MAPRES)
    }
    createCircle(x, y) {
        this.ctx.beginPath();
        this.ctx.arc(x, y, this.RADIUS, 0, this.degToRad(360), true);
        this.ctx.fill();
    }
    canvasMap() {
        for (var i = 0; i < this.mapString.length; i++) {
            var char = this.mapString[i];
            var x = i % this.MAPRES
            var y = Math.trunc((i) / this.MAPRES)
            if (char == 'P') {
                if (!this.playerCreated) {
                    this.x = x * this.MAPRES * 1.5 + this.MAPRES / 2
                    this.y = y * this.MAPRES * 1.5 + this.MAPRES / 2
                    this.playerCreated = true
                }
            }  

        }
        
        for (var i = 0; i < this.mapString.length; i++) {
            var char = this.mapString[i];
            var x = i % this.MAPRES
            var y = Math.trunc((i) / this.MAPRES)
            if (char == '0')
                this.canvasBlock(x, y)
            else if(char =='F'){
                this.canvasFinish(x, y)
            }
             
            
            

        }

    }


    lockChangeAlert() {

        if (document.pointerLockElement === this.canvas || document.mozPointerLockElement === this.canvas) {
            console.log('The pointer lock status is now locked (moz)');
            document.addEventListener("mousemove", this.updatePos, false);
        } else {
            console.log('The pointer lock status is now unlocked');
            document.removeEventListener("mousemove", this.updatePos, false)
            this.canvasMenu()

        }
    }
    updatePosition(e) {
        var self = this
        this.x += e.movementX;
        this.y += e.movementY;
        if (this.x > this.canvas.width + this.RADIUS) {
            this.x = -this.RADIUS;
        }
        if (this.y > this.canvas.height + this.RADIUS) {
            this.y = -this.RADIUS;
        }
        if (this.x < -this.RADIUS) {
            this.x = this.canvas.width + this.RADIUS;
        }
        if (this.y < -this.RADIUS) {
            this.y = this.canvas.height + this.RADIUS;
        }

        if (!this.animation) {
            this.animation = requestAnimationFrame(function () {
                self.animation = null;
                self.canvasDrawLoop();
            });
        }
    }

    leerMapa(file){
        try {

            var lector = new FileReader() 
            var self = this
            lector.onload = function (evento) {
                //El evento "onload" se lleva a cabo cada vez que se completa con éxito una operación de lectura
                //La propiedad "result" es donde se almacena el contenido del archivo
                //Esta propiedad solamente es válida cuando se termina la operación de lectura
                var textString = lector.result
                
                self.mapString=textString
                self.mapString=self.mapString.replace(/[\n\r]+/g, '');
                self.mapString=self.mapString.replace( /[\r\n]+/gm, "" );
                for (var char in self.mapString) {
                    console.log(self.mapString[char]+": "+self.mapString.charCodeAt(char))
                }
            }
            lector.readAsText(file)
            
        } catch (e) {
            alert("Not a ej14map file!") 
        }
    }
    dropHandler(ev) {
        console.log('File(s) dropped');

        // Prevent default behavior (Prevent file from being opened)
        ev.preventDefault();

        if (ev.dataTransfer.items) {
            // Use DataTransferItemList interface to access the file(s)
            for (var i = 0; i < ev.dataTransfer.items.length; i++) {
                // If dropped items aren't files, reject them
                if (ev.dataTransfer.items[i].kind === 'file') {
                    this.leerMapa(ev.dataTransfer.items[i].getAsFile())
                    this.canvasMap()
                }
            }
        } else {
            // Use DataTransfer interface to access the file(s)
            for (var i = 0; i < ev.dataTransfer.files.length; i++) {
                console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
            }
        }

        // Pass event to removeDragData for cleanup
        this.removeDragData(ev)
    }
    dragover_handler(ev) {
        ev.preventDefault();
        ev.dataTransfer.dropEffect = "move"
       }
    removeDragData(ev) {

        if (ev.dataTransfer.items) {
            // Use DataTransferItemList interface to remove the drag data
            ev.dataTransfer.items.clear();
        } else {
            // Use DataTransfer interface to remove the drag data
            ev.dataTransfer.clearData();
        }
    }

    
}
var mazeGame = new MazeGame()

