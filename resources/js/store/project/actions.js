import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const addProject = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/create',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const listProject = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const deleteProject = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/project/delete/'+opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
}
export const detailProject = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/project/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
}

export const listProjectCate = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/category/list', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const addProjectCate = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/category/create', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const detailProjectCate = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/project/category/edit/' + opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const bulkDeleteProject = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/bulk-delete', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const bulkDuplicateProject = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/bulk-duplicate', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const toggleProjectField = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/project/toggle-field', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const deleteProjectCate = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/project/category/delete/' + opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};