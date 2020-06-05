// import Peer from 'peerjs';
//
// const init = () => {
//   const peer = new Peer(null, {
//     debug: 2
//   });
//   peer.on('open', function (id) {
//     // Workaround for peer.reconnect deleting previous id
//     if (peer.id === null) {
//       console.log('Received null id from peer open');
//       peer.id = lastPeerId;
//     } else {
//       lastPeerId = peer.id;
//     }
//
//     console.log('ID: ' + peer.id);
//     recvId.innerHTML = "ID: " + peer.id;
//     status.innerHTML = "Awaiting connection...";
//   });
// }
