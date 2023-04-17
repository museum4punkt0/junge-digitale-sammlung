import TempUsers from "./components/TempUsers.vue";
import TempUser from "./components/TempUser.vue";

panel.plugin("2av/temporaryusers", {
  components: {
    temporaryusers: TempUsers, 
    temporaryuser: TempUser,  
  },
});