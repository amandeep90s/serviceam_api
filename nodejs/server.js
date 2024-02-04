import axios from "axios";
import cors from "cors";
import express from "express";
import http from "http";
import { createClient } from "redis";
import { Server } from "socket.io";

const app = express();

app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const port = process.env.PORT || 8990;
const io = new Server(server);

server.listen(port, () => console.log(`Server is running on port ${port}...`));

const redisClient = createClient();

redisClient.on("error", (err) => console.log("Redis Client Error", err));
await redisClient.connect();

// subscribe to channels
const channelsToSubscribe = [
    "newRequest",
    "checkTransportRequest",
    "checkOrderRequest",
    "checkServiceRequest",
    "providerUpdate",
    "settingsUpdate",
    "paymentUpdate",
];

redisClient.subscribe(channelsToSubscribe);

// Handle incoming Redis messages
redisClient.on("message", (channel, requestData) => {
    const data = JSON.parse(requestData);

    // Handle different channels
    switch (channel) {
        case "newRequest":
            handleNewRequest(data);
            break;
        case "providerUpdate":
            handleProviderUpdate(data);
            break;
        case "paymentUpdate":
            handlePaymentUpdate(data);
            break;
        case "settingsUpdate":
            handleSettingsUpdate(data);
            break;
        case "checkTransportRequest":
            handleCheckRequest(data, "rideRequest");
            break;
        case "checkServiceRequest":
            handleCheckRequest(data, "serveRequest");
            break;
        case "checkOrderRequest":
            handleCheckRequest(data, "orderRequest");
            break;
        default:
            break;
    }
});

// Socket.io connection logic
io.sockets.on("connection", (socket) => {
    socket.on("joinCommonRoom", (newroom) => joinRoom(io, socket, newroom));
    socket.on("joinCommonProviderRoom", (newroom) =>
        joinRoom(io, socket, newroom)
    );
    socket.on("joinCommonUserRoom", (newroom) => joinRoom(io, socket, newroom));
    socket.on("joinShopRoom", (newroom) => joinRoom(io, socket, newroom));
    socket.on("joinPrivateRoom", (newroom) => joinRoom(io, socket, newroom));
    socket.on("joinPrivateChatRoom", (newroom) =>
        joinRoom(io, socket, newroom)
    );
    socket.on("leaveRoom", (newroom) => leaveRoom(socket, newroom));
    socket.on("send_location", (data) => handleLocation(data));
    socket.on("update_location", (data) => handleUpdateLocation(data));
    socket.on("send_message", (data) => handleMessage(data));
    socket.on("disconnect", () => {});
});

function emitMessage(room = "", eventName = "", message = "") {
    if (room) {
        io.sockets.in(room).emit(eventName, message);
    } else {
        io.emit(eventName, message);
    }
}

// Functions for handling different events
function handleNewRequest(data) {
    emitMessage(
        data.room,
        "newRequest",
        `New request created in common ${data.room}`
    );

    if (data.city !== "" || data.city === 0) {
        const provider_room = `${data.room}_${data.city}`;
        emitMessage(
            provider_room,
            "newRequest",
            `New request created for providers in ${provider_room}`
        );
    }

    if (data.user !== "") {
        const user_room = `${data.room}_${data.user}_USER`;
        emitMessage(
            user_room,
            "newRequest",
            `New request created for user in ${user_room}`
        );
    }

    if (data.shop !== "" && data.type === "ORDER") {
        const shop_room = `${data.room}_shop_${data.shop}`;
        emitMessage(
            shop_room,
            "newRequest",
            `New shop request created in ${shop_room}`
        );
    }
}

function handleProviderUpdate(data) {
    const provider_room = data.room;
    emitMessage(
        provider_room,
        "approval",
        `New document request created in ${provider_room}`
    );
}

function handlePaymentUpdate(data) {
    const room = `${data.room}_R${data.id}_${data.type}`;
    const nodeName = data.type === "TRANSPORT" ? "rideRequest" : "serveRequest";
    emitMessage(room, nodeName, { payment_mode: data.payment_mode });
}

function handleSettingsUpdate(data) {
    if (data.type === "SETTING" || data.type === "SERVICE_SETTING") {
        const eventName =
            data.type === "SETTING" ? "settingUpdate" : "serviceSettingUpdate";
        emitMessage("", eventName, `Settings updated`);
    }
}

function handleCheckRequest(data, eventName) {
    if (
        data.type === "TRANSPORT" ||
        data.type === "SERVICE" ||
        data.type === "ORDER"
    ) {
        const room = `${data.room}_R${data.id}_${data.type}`;
        emitMessage(
            room,
            eventName,
            `New ${data.type.toLowerCase()} request created in ${room}`
        );
    }
}

function joinRoom(io, socket, newroom) {
    const rooms = io.sockets.adapter.sids[socket.id];
    for (const room in rooms) {
        socket.leave(room);
    }
    socket.join(newroom);
    emitMessage(newroom, "socketStatus", `you are connected to ${newroom}`);
}

function leaveRoom(socket, newroom) {
    const rooms = io.sockets.adapter.sids[socket.id];
    for (const room in rooms) {
        if (room === newroom) {
            socket.leave(room);
        }
    }
}

function handleLocation(data) {
    emitMessage(
        data.room,
        "socketStatus",
        `you are receiving message in ${data.room}`
    );
    emitMessage(data.room, "updateLocation", {
        lat: data.latitude,
        lng: data.longitude,
    });
}

function handleUpdateLocation(data) {
    emitMessage(
        data.room,
        "socketStatus",
        `you are receiving message in ${data.room}`
    );

    axios
        .post(data.url, {
            provider_id: data.provider_id,
            latitude: data.latitude,
            longitude: data.longitude,
        })
        .then((response) => {})
        .catch((error) => {});
}

function handleMessage(data) {
    emitMessage(
        data.room,
        "socketStatus",
        `you are receiving message in ${data.room}`
    );

    emitMessage(data.room, "new_message", {
        type: data.type,
        message: data.message,
        user: data.user,
        provider: data.provider,
    });

    axios
        .post(data.url, {
            id: data.id,
            admin_service: data.admin_service,
            salt_key: data.salt_key,
            user_name: data.user,
            provider_name: data.provider,
            type: data.type,
            message: data.message,
        })
        .then((response) => console.log(response))
        .catch((error) => console.log(error));
}
