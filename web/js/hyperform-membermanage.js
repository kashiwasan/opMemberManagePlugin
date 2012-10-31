var validator = {
  alphanumeric: {
    warning: 'Please enter only alphanumeric characters.',
    regex  : '/^[a-zA-Z0-9]+$/i'
  },
  numbers: {
    warning: 'Please enter numbers only.',
    regex  : '/^[0-9]+$/i'
  },
  email: {
    warning: 'Please enter correct email address.',
    regex  : '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i'
  }
  url: {
    warning: 'Please enter correct url.',
    regex  : '~^(%s)://(([a-z0-9-]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:[0-9]+)?(/?|/\S+)$~ix'
  }
};

var form = new Hyperform({
  formWidth  : '100%',
  labelWidth : '130px',//default: 120px
  labelAlign : 'right',//default: left
  submitLabel: 'go',//default: Submit
   
  onSubmit   : function(result){
    console.log(result);
  },

  fields: [
  //TODO: fields の定義をapiで取得
    {
      key  : 'text',
      label: 'Text',
      input: { type: 'text' }
    },
    {
      key  : 'appendedText',
      label: 'Appended text',
      input: {
        type       : 'text',
        width      : 100,
        placeholder: 'placeholder',
        appendText : '@akkar.in'
       }
     }
   ]
});
 
form.render(container);
