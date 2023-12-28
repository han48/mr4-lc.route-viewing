window.wsSendViewing = (action = 'open') => {
    if (conn.readyState === 1) {
        let session = null
        let config = "0"
        if (typeof sessionId !== 'undefined') {
            session = sessionId
        }
        if (typeof wsConfig !== 'undefined') {
            config = wsConfig
        }
        const data = {
            'action': action,
            'type': 'route_viewings',
            'href': location.href,
            'path': location.pathname,
            'session_id': session,
            'config': config,
        }
        conn.send(JSON.stringify(data))
    }
}

window.onbeforeunload = function () {
    window.wsSendViewing('close')
}

function wsConnect() {
    if (typeof wsUrl === 'undefined' || undefined === wsUrl || null === wsUrl || wsUrl.length === 0) {
        return
    }
    window.conn = new WebSocket(wsUrl)
    conn.onopen = function (e) {
        window.wsSendViewing()
        console.log("OPEN", e)
    }
    conn.onmessage = function (e) {
        if (e.data) {
            const msg = JSON.parse(e.data)
            console.log(msg)
            if (Number(msg.status_code) === 302) {
                const template = document.getElementById('mr4-lc-route-viewing-popup')
                if (undefined !== template && null !== template) {
                    const obj = template.content.cloneNode(true)
                    const div = document.createElement('div')
                    div.appendChild(obj)
                    div.innerHTML = div.innerHTML.replace('|||message|||', msg.data.message)
                    document.body.appendChild(div)
                } else {
                    alert(msg.data.message.replace("<br />", "\n"));
                }
            } else {
                const div = document.getElementById('route-viewing-popup')
                if (undefined !== div && null !== div) {
                    document.body.removeChild(div)
                }
            }
        }
    }
    conn.onerror = function (e) {
        window.wsSendViewing('close')
        console.log("ERROR", e)
        conn.close()
    }
    conn.onclose = function (e) {
        window.wsSendViewing('close')
        console.log("CLOSE", e)
        setTimeout(() => {
            wsConnect()
        }, 1000)
    }
}

wsConnect()
