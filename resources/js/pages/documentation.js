import Alpine from "alpinejs";
import Cookies from 'js-cookie';
import axios from "axios";

function checkCode(code, callback) {
    axios.get("/api/invite/check/"+code)
        .then((response)=>{
            if (response.data.success) {
                callback(response);
            }

        })
}

Alpine.data("pageDocs", ()=>({
    showPopup: true,

    init() {
        const cookieToken = Cookies.get("access-token");
        if (cookieToken !== undefined) {
            checkCode(cookieToken, (response)=>{
                this.showPopup = false;
            })
        }

    },

    openPopup(url) {
        //this.showPopup = true;
        location.href = url;
    },

    checkAccessCode() {
        let token = this.$refs.inviteCode.value;

        checkCode(token, (response)=>{
            Cookies.set("access-token", token);
            this.showPopup = false;
        })
    },

    closeModel() {
        this.showPopup = false;
    }
}));

Alpine.start()
