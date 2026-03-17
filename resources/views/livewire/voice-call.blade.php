<div>
    <div x-data="voiceCall('{{ $userId }}')" class="max-w-4xl mx-auto p-6 bg-white border rounded shadow-lg mt-10">
        <div class="mb-4 flex justify-between items-center">
            <div>
                <span class="font-bold">Your ID:</span> <code class="bg-gray-100 p-1 rounded">{{ $userId }}</code>
                <div class="text-xs mt-1 text-gray-500 font-mono">ICE: <span x-text="iceState" :class="{ 'text-green-600': iceState === 'connected', 'text-red-600': iceState === 'failed', 'text-orange-500': iceState === 'checking' }"></span></div>
            </div>
            <div x-show="onCall" class="text-green-600 font-bold animate-pulse text-sm">● LIVE (RELAY)</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 bg-gray-900 rounded-lg p-2 min-h-[300px]">
            <div class="relative bg-black rounded overflow-hidden flex items-center justify-center">
                <video id="localVideo" autoplay muted playsinline class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
            </div>
            <div class="relative bg-black rounded overflow-hidden flex items-center justify-center">
                <video id="remoteVideo" autoplay playsinline class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
            </div>
        </div>

        <div class="space-y-4 text-center">
            <div x-show="!localStream">
                <button @click="startCamera()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Enable Camera</button>
            </div>

            <div x-show="localStream && !isCalling && !isRinging && !onCall">
                <div class="flex gap-2">
                    <input type="text" x-model="remoteUserId" class="flex-1 border rounded p-2 text-sm" placeholder="Paste Remote ID">
                    <button @click="startCall()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm">Call</button>
                </div>
            </div>

            <div x-show="isRinging" class="bg-yellow-50 p-4 rounded border border-yellow-200">
                <p class="mb-2 text-sm">Incoming call...</p>
                <button @click="answerCall()" class="bg-green-600 text-white px-10 py-2 rounded font-bold">Answer</button>
            </div>

            <div x-show="onCall || isCalling || iceState === 'failed'">
                <button @click="hangUp()" class="bg-red-600 text-white px-10 py-2 rounded">End / Reset</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('voiceCall', (userId) => ({
                userId: userId,
                remoteUserId: '',
                isCalling: false,
                isRinging: false,
                onCall: false,
                iceState: 'disconnected',
                incomingCallerId: '',
                peerConnection: null,
                localStream: null,
                pendingOffer: null,
                candidateBuffer: [],
                iceServers: { 
                    iceServers: [
                        {
                            urls: "turns:global.relay.metered.ca:443?transport=tcp",
                            username: "{{ $turnUsername }}",
                            credential: "{{ $turnCredential }}",
                        },
                        {
                            urls: "turn:global.relay.metered.ca:443",
                            username: "{{ $turnUsername }}",
                            credential: "{{ $turnCredential }}",
                        }
                    ] 
                },

                async init() {
                    await this.startCamera();
                    window.Echo.channel('voice-call-channel').listen('WebRTCSignaling', (e) => {
                        if (e.receiverId === this.userId) this.handleSignal(e);
                    });
                },

                async startCamera() {
                    try {
                        this.localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                        document.getElementById('localVideo').srcObject = this.localStream;
                    } catch (e) { console.error(e); }
                },

                async startCall() {
                    this.isCalling = true;
                    this.peerConnection = this.createPeerConnection(this.remoteUserId);
                    const offer = await this.peerConnection.createOffer();
                    await this.peerConnection.setLocalDescription(offer);
                    @this.sendSignal(this.remoteUserId, { type: 'offer', sdp: offer.sdp });
                },

                async handleSignal(e) {
                    const signal = e.data;
                    if (signal.type === 'offer' && !this.onCall && !this.isRinging) {
                        this.incomingCallerId = e.senderId;
                        this.isRinging = true;
                        this.pendingOffer = signal;
                    } else if (signal.type === 'answer' && this.peerConnection) {
                        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(signal));
                        this.isCalling = false;
                        this.processBufferedCandidates();
                    } else if (signal.type === 'candidate') {
                        if (this.peerConnection && this.peerConnection.remoteDescription) {
                            await this.peerConnection.addIceCandidate(new RTCIceCandidate(signal.candidate)).catch(() => {});
                        } else {
                            this.candidateBuffer.push(signal.candidate);
                        }
                    } else if (signal.type === 'hangup') {
                        window.location.reload();
                    }
                },

                async hangUp() {
                    if (this.remoteUserId) {
                        @this.sendSignal(this.remoteUserId, { type: 'hangup' });
                    }
                    window.location.reload();
                },

                async answerCall() {
                    this.isRinging = false;
                    this.remoteUserId = this.incomingCallerId;
                    this.peerConnection = this.createPeerConnection(this.remoteUserId);
                    await this.peerConnection.setRemoteDescription(new RTCSessionDescription(this.pendingOffer));
                    const answer = await this.peerConnection.createAnswer();
                    await this.peerConnection.setLocalDescription(answer);
                    @this.sendSignal(this.remoteUserId, { type: 'answer', sdp: answer.sdp });
                    this.processBufferedCandidates();
                },

                async processBufferedCandidates() {
                    while (this.candidateBuffer.length > 0) {
                        const candidate = this.candidateBuffer.shift();
                        if (this.peerConnection && this.peerConnection.remoteDescription) {
                            await this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate)).catch(() => {});
                        }
                    }
                },

                createPeerConnection(targetId) {
                    const pc = new RTCPeerConnection({
                        ...this.iceServers,
                        iceTransportPolicy: 'relay' // FORCE USE OF TURN SERVER
                    });
                    
                    this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
                    
                    pc.onicecandidate = (event) => {
                        if (event.candidate && event.candidate.candidate.includes('typ relay')) {
                            @this.sendSignal(targetId, { type: 'candidate', candidate: event.candidate });
                        }
                    };

                    pc.oniceconnectionstatechange = () => {
                        this.iceState = pc.iceConnectionState;
                        if (this.iceState === 'connected' || this.iceState === 'completed') this.onCall = true;
                    };

                    pc.ontrack = (event) => {
                        console.log('[Track] Received RELAY stream');
                        const remoteVideo = document.getElementById('remoteVideo');
                        if (remoteVideo.srcObject !== event.streams[0]) {
                            remoteVideo.srcObject = event.streams[0];
                            remoteVideo.play().catch(() => {});
                        }
                    };
                    return pc;
                }
            }));
        });
    </script>
</div>
