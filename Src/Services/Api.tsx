import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost/cotacao-2021/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'token': 'eaf5add4fc648077df8e0651221a6d73a7a2422c'
       }
})

export default api