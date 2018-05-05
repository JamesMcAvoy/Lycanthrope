/**
 * Lycanthrope JS
 */

;((global, $) => {

    // Version actuelle du script
    let version = 'alpha'

    // Retourne un string formate
    const printf = (string, args = []) => {
        for(i in args) {
            string = string.replace('{' + i + '}', args[i])
        }
        return string
    }

    // Retourne un booleen en fonction de l'existance de l'attribut
    const isset = (attr) => {
        return (typeof attr !== 'undefined')
    }

    // Méthode superglobale qui exécute les autres méthodes de l'objet
    // Exécute Lycanthrope.connect()
    //
    //
    var init = function(args = {}) {
        this.connect(args)
    }

    // Instancie une connexion avec le serveur WS
    // Le token dans l'objet en paramètre est un token CSRF necessaire
    // lors de la connexion pour securiser l'application et le client
    // Il est possible de passer des paramètres additionnels via l'attribut routeArgs et la route
    // Il est aussi possible de modifier le protocole en WSS (secure)
    // Exemple :
    //
    // Lycanthrope.connect({
    //      pseudo: 'user',
    //      token: 'token',
    //      socket: '127.0.0.1:8888',
    //      route: '/?pseudo={0}&token={1}&room={2}',
    //      routeArgs: ['id_room'],
    //      protocol: 'wss'
    // })
    var connect = function(args) {
        if(!isset(args.pseudo) || !isset(args.token)) {
            console.log('Erreur lors de la connexion : pseudo/token manquant')
            return;
        }

        socket   = isset(args.socket)   ? args.socket   : '127.0.0.1:8888'
        route    = isset(args.route)    ? args.route    : '/?pseudo={0}&token={1}'
        protocol = isset(args.protocol) ? args.protocol : 'ws'

        let routeArgs
        // Si paramètre routeArgs existe et la route n'est pas celle par defaut
        // Dans ce cas la route avec les paramètres additionnels va etre creee
        if(isset(args.route) && isset(args.routeArgs)) {
            routeArgs = [args.pseudo, args.token].concat(args.routeArgs)
        } else {
            routeArgs = [args.pseudo, args.token]
        }

        route = protocol+'://'+socket+printf(route, routeArgs)

        try {
            this.socket = new WebSocket(route)

            this.socket.onopen    = onOpen
            this.socket.onclose   = onClose
            this.socket.onmessage = onMessage
            this.socket.onerror   = onError
        } catch(e) {
            console.error(e, e.stack)
        } finally {
            this.pseudo = args.pseudo
            this.token = args.token
        }
    }

    // Méthodes this.socket asynchrones

    // On open connection
    //
    let onOpen = function() {

    }

    //
    let onClose = function() {

    }

    //
    let onMessage = function(msg) {
        console.log(msg)
    }

    //
    let onError = function(e) {

    }

    // Envoi message
    // Prend en paramètre le message et le type de message
    // La méthode envoie un JSON parsé en string avec le pseudo et le token
    var send = function(msg, type = 'MSG') {
        this.socket.send(JSON.stringify({
            msg: msg,
            type: type,
            user: {
                pseudo: this.pseudo,
                token: this.token
            }
        }))
    }

    // Main Lycanthrope object
    var main = {
        // this.socket
        // this.pseudo
        // this.token
        init: init,
        connect: connect,
        send: send,

        v: version
    }

    // Charge l'objet dans la page actuelle
    global.prototype.Lycanthrope = main

})(Window, jQuery)